// index.js

const mysql = require('mysql2/promise');
const WebSocket = require('ws');
const express = require('express');
const http = require('http');
const { v4: uuidv4 } = require('uuid');

// --- Настройки подключения к БД ---
const dbConfig = {
  host: '127.0.0.1',
  user: 'combats_user',
  password: '12345678',
  database: 'combats'
};

// --- Хранилище пользователей для WebSocket ---
const users = {};

// --- Вспомогательная функция для генерации случайного числа ---
function randomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

// --- Расчет физического урона с учетом защиты ---
function calculateDamage(damage, defence) {
  const reduction = Math.min(defence * 0.3, damage * 0.7);
  let finalDamage = Math.floor(damage - reduction);
  if (finalDamage < 1) finalDamage = 1;
  return finalDamage;
}

// --- Расчет магического урона с учетом магической защиты ---
function calculateMagicDamage(damage, mdef) {
  const reduction = Math.min(mdef * 0.3, damage * 0.7);
  let finalDamage = Math.floor(damage - reduction);
  if (finalDamage < 1) finalDamage = 1;
  return finalDamage;
}

// --- Проверка критического удара ---
function isCritical(crit, anticrit) {
  let chance = crit - anticrit;
  if (chance <= 0) return false;
  chance = Math.min(chance, 1000);
  const roll = randomInt(1, 1000);
  return roll <= chance;
}

// --- Проверка уворота ---
function isEvasion(evaision, aeveision) {
  let chance = evaision - aeveision;
  if (chance <= 0) return false;
  chance = Math.min(chance, 1000);
  const roll = randomInt(1, 1000);
  return roll <= chance;
}

// --- Функция для записи лога ---
async function addBattleLog(connection, battleId, userId, enemyId, logMessage) {
  try {
    await connection.execute(
      "INSERT INTO battle_log (battle_id, user_id, enemy_id, log, attack_time) VALUES (?, ?, ?, ?, ?)",
      [battleId, userId || 0, enemyId || 0, logMessage, Math.floor(Date.now() / 1000)]
    );
    console.log(`  📝 Log: ${logMessage.replace(/<[^>]*>/g, '')}`);
  } catch (error) {
    console.error('Error adding battle log:', error);
  }
}

// --- Функция для отправки приватного сообщения в чат ---
async function sendChatMessage(connection, userId, message, battleId) {
  try {
    const currentTime = Math.floor(Date.now() / 1000);
    await connection.execute(
      "INSERT INTO chat (message, isPrivate, from_user, to_user, create_time) VALUES (?, ?, ?, ?, ?)",
      [message, 1, 0, userId, currentTime]
    );
    console.log(`  💬 Chat message sent to user ${userId}: ${message}`);
  } catch (error) {
    console.error('Error sending chat message:', error);
  }
}

// --- Функция проверки и обновления IsAlive для всех участников боя ---
async function updateAllIsAlive(connection, battleId) {
  const [participants] = await connection.execute(
    "SELECT * FROM user_battle WHERE battle_id = ?",
    [battleId]
  );
  
  for (const participant of participants) {
    if (participant.hp <= 0 && (participant.IsAlive === 1 || participant.IsAlive === true)) {
      console.log(`  💀 Setting IsAlive=0 for ${participant.user_id ? 'User' : 'Bot'} ${participant.user_id || participant.bot_id}`);
      await connection.execute(
        "UPDATE user_battle SET IsAlive = 0 WHERE id = ?",
        [participant.id]
      );
    }
  }
}

// --- Обработка обычного удара или скилла ---
async function processNormalAttack(connection, attack, attacker, defender, battleId, totalDamageMap, attackerStats, defenderStats, fullUserData, battleUsers) {
  
  // Формируем имена
  const isAttackerBot = attacker && (attacker.isBot === true || attacker.isBot === 1);
  const isDefenderBot = defender && (defender.isBot === true || defender.isBot === 1);
  
  const attackerName = isAttackerBot ? `[BOT] ${attacker.username || attacker.bot_id || 'Unknown'}` : `[PLAYER] ${attacker.username || 'Player'}`;
  const defenderName = isDefenderBot ? `[BOT] ${defender.username || defender.bot_id || 'Unknown'}` : `[PLAYER] ${defender.username || 'Player'}`;
  
  console.log(`  Attack: ${attackerName} (ID:${attacker.id}) -> ${defenderName} (ID:${defender.id})`);
  
  // Проверка что оба живы
  if (attacker.hp <= 0) {
    console.log(`  ⚠️ ${attackerName} is dead, skipping attack`);
    return { attacker, defender };
  }
  
  if (defender.hp <= 0) {
    console.log(`  ⚠️ ${defenderName} is dead, skipping attack`);
    return { attacker, defender };
  }
  
  // ===== ОБРАБОТКА СКИЛЛОВ =====
  if (attack.skill && attack.skill !== 'null' && attack.skill !== null) {
    console.log(`  ✨ Processing skill: ${attack.skill}`);
    
    switch (attack.skill) {
      case 'earth': {
        if (attacker.isBot === true || attacker.isBot === 1) {
          console.log(`  ⚠️ Bot attempted to use EARTH skill, skipping`);
          break;
        }
        
        const [currentBattleUsers] = await connection.execute(
          "SELECT * FROM user_battle WHERE battle_id = ?",
          [battleId]
        );
        
        const enemies = currentBattleUsers.filter(u => 
          u.komand !== attacker.komand && (u.IsAlive === 1 || u.IsAlive === true) && u.hp > 0
        );
        
        let totalEarthDamage = 0;
        
        for (const enemy of enemies) {
          const enemyId = enemy.user_id || enemy.bot_id;
          const enemyStats = fullUserData[enemyId];
          
          if (!enemyStats) continue;
          
          const isEnemyBot = (enemy.bot_id !== null);
          const enemyName = isEnemyBot ? `[BOT] ${enemyStats.username || enemy.bot_id}` : `[PLAYER] ${enemyStats.username || 'Player'}`;
          
          let earthDamage = Math.max(1, Math.floor(attackerStats.earth * 1.5 + attackerStats.inte * 0.8));
          
          // Применяем МАГИЧЕСКУЮ защиту (mdef) для скилла earth
          let actualDamage = calculateMagicDamage(earthDamage, enemyStats.mdef || 0);
          let currentShield = enemy.shild || 0;
          
          if (currentShield > 0) {
            const shieldAbsorb = Math.min(currentShield, actualDamage);
            currentShield -= shieldAbsorb;
            actualDamage -= shieldAbsorb;
            await connection.execute(
              "UPDATE user_battle SET shild = ? WHERE id = ?",
              [currentShield, enemy.id]
            );
          }
          
          const minPassDamage = Math.max(1, Math.floor(earthDamage * 0.3));
          if (actualDamage < minPassDamage) actualDamage = minPassDamage;
          if (actualDamage < 1) actualDamage = 1;
          
          const oldHp = enemy.hp;
          let newHp = oldHp - actualDamage;
          
          console.log(`    ${enemyName}: HP ${oldHp} → ${newHp} (magic damage: ${actualDamage}, mdef: ${enemyStats.mdef || 0})`);
          
          enemy.hp = newHp;
          await connection.execute(
            "UPDATE user_battle SET hp = ? WHERE id = ?",
            [enemy.hp, enemy.id]
          );
          totalEarthDamage += actualDamage;
          
          if (enemy.hp <= 0) {
            console.log(`  💀 ${enemyName} УБИТ!`);
            await connection.execute(
              "UPDATE user_battle SET IsAlive = 0 WHERE id = ?",
              [enemy.id]
            );
          }
        }
        
        const attackerKey = `${attacker.user_id || attacker.bot_id}_${isAttackerBot ? 'bot' : 'player'}`;
        if (!totalDamageMap[attackerKey]) totalDamageMap[attackerKey] = 0;
        totalDamageMap[attackerKey] += totalEarthDamage;
        
        const logMessage = `<span style="color: orange;">${attackerName} использовал КАМНЕПАД и нанес ${totalEarthDamage} урона всем врагам!</span>`;
        await addBattleLog(connection, battleId, attacker.user_id || attacker.bot_id, 0, logMessage);
        break;
      }
      
      case 'fire': {
        if (attacker.isBot === true || attacker.isBot === 1) {
          console.log(`  ⚠️ Bot attempted to use FIRE skill, skipping`);
          break;
        }
        
        if (!defender || !defenderStats) {
          console.log(`  ⚠️ FIRE skill: invalid defender, skipping`);
          break;
        }
        
        console.log(`  🔥 FIRE SKILL DEBUG:`);
        console.log(`     Attacker: ${attackerName}, fire=${attackerStats.fire}, inte=${attackerStats.inte}`);
        console.log(`     Defender: ${defenderName}, current HP=${defender.hp}, shield=${defender.shield}, mdef=${defenderStats.mdef || 0}`);
        
        let fireDamage = Math.max(1, Math.floor(attackerStats.fire * 2 + attackerStats.inte * 1.2));
        console.log(`     Base fire damage: ${fireDamage}`);
        
        const isCriticalHit = isCritical(attackerStats.crit, defenderStats.anticrit);
        if (isCriticalHit) {
          fireDamage = Math.max(1, Math.floor(fireDamage * 1.5));
          console.log(`     Critical! Damage increased to: ${fireDamage}`);
        }
        
        // Применяем МАГИЧЕСКУЮ защиту (mdef) для скилла fire
        let actualDamage = calculateMagicDamage(fireDamage, defenderStats.mdef || 0);
        let currentShield = defender.shield || 0;
        console.log(`     Initial shield: ${currentShield}, mdef: ${defenderStats.mdef || 0}`);
        
        if (currentShield > 0) {
          const shieldAbsorb = Math.min(currentShield, actualDamage);
          currentShield -= shieldAbsorb;
          actualDamage -= shieldAbsorb;
          defender.shield = currentShield;
          await connection.execute(
            "UPDATE user_battle SET shild = ? WHERE id = ?",
            [currentShield, defender.id]
          );
          console.log(`     Shield absorbed ${shieldAbsorb}, remaining shield: ${currentShield}, damage after shield: ${actualDamage}`);
        }
        
        const minPassDamage = Math.max(1, Math.floor(fireDamage * 0.3));
        if (actualDamage < minPassDamage) actualDamage = minPassDamage;
        if (actualDamage < 1) actualDamage = 1;
        console.log(`     Min pass damage (30%): ${minPassDamage}, final damage: ${actualDamage}`);
        
        const oldHp = defender.hp;
        let newHp = oldHp - actualDamage;
        
        console.log(`     🔥 APPLYING DAMAGE: ${oldHp} - ${actualDamage} = ${newHp}`);
        
        if (newHp > oldHp) {
          console.log(`     ❌❌❌ ERROR: New HP (${newHp}) is GREATER than old HP (${oldHp})!`);
          console.log(`     This should NEVER happen! Damage was ${actualDamage}`);
          break;
        }
        
        defender.hp = newHp;
        await connection.execute(
          "UPDATE user_battle SET hp = ? WHERE id = ?",
          [defender.hp, defender.id]
        );
        
        const [verifyHp] = await connection.execute(
          "SELECT hp FROM user_battle WHERE id = ?",
          [defender.id]
        );
        console.log(`     VERIFY: DB says HP = ${verifyHp[0]?.hp}`);
        
        if (defender.hp <= 0) {
          console.log(`  💀 ${defenderName} УБИТ!`);
          await connection.execute(
            "UPDATE user_battle SET IsAlive = 0 WHERE id = ?",
            [defender.id]
          );
          defender.IsAlive = 0;
        }
        
        const attackerKey = `${attacker.user_id || attacker.bot_id}_${isAttackerBot ? 'bot' : 'player'}`;
        if (!totalDamageMap[attackerKey]) totalDamageMap[attackerKey] = 0;
        totalDamageMap[attackerKey] += actualDamage;
        
        const critText = isCriticalHit ? 'КРИТИЧЕСКИЙ ' : '';
        const logMessage = `<span style="color: red;">${attackerName} использовал ${critText}МЕТЕОРИТ на ${defenderName} и нанес ${actualDamage} урона!</span>`;
        await addBattleLog(connection, battleId, attacker.user_id || attacker.bot_id, defender.user_id || defender.bot_id, logMessage);
        break;
      }
      
      case 'water': {
        if (attacker.isBot === true || attacker.isBot === 1) {
          console.log(`  ⚠️ Bot attempted to use WATER skill, skipping`);
          break;
        }
        
        console.log(`  💚 Water healing for team of ${attackerName} (team ${attacker.komand})`);
        
        const [currentBattleUsers] = await connection.execute(
          "SELECT * FROM user_battle WHERE battle_id = ?",
          [battleId]
        );
        
        const allies = currentBattleUsers.filter(u => 
          u.komand === attacker.komand && (u.IsAlive === 1 || u.IsAlive === true) && u.hp > 0
        );
        
        console.log(`  Found ${allies.length} allies to heal`);
        
        let totalHeal = 0;
        
        for (const ally of allies) {
          const allyId = ally.user_id || ally.bot_id;
          const allyStats = fullUserData[allyId];
          
          if (!allyStats) continue;
          
          const isAllyBot = (ally.bot_id !== null && ally.bot_id !== undefined);
          const allyName = isAllyBot ? `[BOT] ${allyStats.username || ally.bot_id}` : `[PLAYER] ${allyStats.username || 'Player'}`;
          
          let healAmount = Math.max(1, Math.floor(attackerStats.water * 1.2 + attackerStats.inte * 0.6));
          const maxHp = allyStats.health;
          const currentHp = ally.hp;
          const newHp = Math.min(currentHp + healAmount, maxHp);
          const actualHeal = newHp - currentHp;
          
          if (actualHeal > 0) {
            await connection.execute(
              "UPDATE user_battle SET hp = ? WHERE id = ?",
              [newHp, ally.id]
            );
            
            totalHeal += actualHeal;
            console.log(`    ${allyName} healed +${actualHeal} HP (${currentHp} → ${newHp})`);
            
            const attackerKey = `${attacker.user_id || attacker.bot_id}_${isAttackerBot ? 'bot' : 'player'}`;
            if (!totalDamageMap[attackerKey]) totalDamageMap[attackerKey] = 0;
            totalDamageMap[attackerKey] += actualHeal;
          }
        }
        
        const logMessage = `<span style="color: green;">${attackerName} использовал ИСЦЕЛЕНИЕ и восстановил ${totalHeal} HP союзникам!</span>`;
        await addBattleLog(connection, battleId, attacker.user_id || attacker.bot_id, 0, logMessage);
        break;
      }
      
      case 'air': {
        if (attacker.isBot === true || attacker.isBot === 1) {
          console.log(`  ⚠️ Bot attempted to use AIR skill, skipping`);
          break;
        }
        
        console.log(`  💨 Air shield on SELF: ${attackerName}`);
        
        let shieldAmount = Math.max(1, Math.floor(attackerStats.air * 1.5 + attackerStats.inte * 0.8));
        const maxShield = Math.floor(attackerStats.health * 0.5);
        attacker.shield = (attacker.shield || 0) + shieldAmount;
        if (attacker.shield > maxShield) attacker.shield = maxShield;
        
        await connection.execute(
          "UPDATE user_battle SET shild = ? WHERE id = ?",
          [attacker.shield, attacker.id]
        );
        
        const logMessage = `<span style="color: yellow;">${attackerName} использовал ВОЗДУШНЫЙ ЩИТ, поглощение ${attacker.shield} урона!</span>`;
        await addBattleLog(connection, battleId, attacker.user_id || attacker.bot_id, 0, logMessage);
        break;
      }
        
      default:
        console.log(`  Unknown skill: ${attack.skill}`);
    }
    
    return { attacker, defender };
  }
  
  // ===== ОБЫЧНАЯ АТАКА (без скилла) - физическая =====
  const attackPart = attack.attack;
  const blockPart = attack.block || 0;
  
  const attackZones = {
    1: 'голову',
    2: 'корпус',
    3: 'пах',
    4: 'ноги'
  };
  
  const blockZones = {
    0: [],
    1: ['голову', 'корпус'],
    2: ['корпус', 'пах'],
    3: ['пах', 'ноги'],
    4: ['ноги', 'голову']
  };
  
  const attackZone = attackZones[attackPart] || 'неизвестно';
  const blockZone = blockZones[blockPart];
  
  let isBlocked = false;
  if (blockZone && attackZone !== 'неизвестно' && blockZone.includes(attackZone)) {
    isBlocked = true;
    console.log(`  🛡️ Блок сработал!`);
  }
  
  let isEvaded = false;
  let counterDamage = 0;
  
  if (!isBlocked && defenderStats) {
    isEvaded = isEvasion(attackerStats.evaision, defenderStats.aeveision);
    if (isEvaded) {
      counterDamage = Math.max(1, Math.floor(calculateDamage(attackerStats.damage, defenderStats.defence) * 0.7));
      console.log(`  💨 Уворот! Контрудар: ${counterDamage}`);
    }
  }
  
  let damage = calculateDamage(attackerStats.damage, defenderStats ? defenderStats.defence : 0);
  let isCriticalHit = false;
  
  if (!isBlocked && !isEvaded && defenderStats) {
    isCriticalHit = isCritical(attackerStats.crit, defenderStats.anticrit);
    if (isCriticalHit) {
      damage = Math.max(1, Math.floor(damage * 1.5));
      console.log(`  ⚡ КРИТИЧЕСКИЙ удар! Урон: ${damage}`);
    }
  }
  
  if (isBlocked) {
    damage = Math.max(1, Math.floor(damage * 0.5));
    console.log(`  🛡️ Урон с блоком: ${damage}`);
  }
  
  if (damage < 1) damage = 1;
  
  let actualDamage = damage;
  let currentShield = defender.shield || 0;
  
  if (currentShield > 0) {
    const shieldAbsorb = Math.min(currentShield, actualDamage);
    currentShield -= shieldAbsorb;
    actualDamage -= shieldAbsorb;
    console.log(`  🛡️ Щит поглотил ${shieldAbsorb} из ${damage} урона`);
    defender.shield = currentShield;
    await connection.execute(
      "UPDATE user_battle SET shild = ? WHERE id = ?",
      [defender.shield, defender.id]
    );
  }
  
  const minPassDamage = Math.max(1, Math.floor(damage * 0.3));
  if (actualDamage < minPassDamage && damage > 0) {
    actualDamage = minPassDamage;
    console.log(`  🛡️ Принудительный минимальный урон: ${actualDamage} (30% от ${damage})`);
  }
  if (actualDamage < 1) actualDamage = 1;
  
  const oldHp = defender.hp;
  let newHp = oldHp - actualDamage;
  
  console.log(`  💔 Урон: ${actualDamage}, HP: ${oldHp} → ${newHp}`);
  
  defender.hp = newHp;
  await connection.execute(
    "UPDATE user_battle SET hp = ? WHERE id = ?",
    [defender.hp, defender.id]
  );
  
  if (defender.hp <= 0) {
    console.log(`  💀 ${defenderName} УБИТ!`);
    await connection.execute(
      "UPDATE user_battle SET IsAlive = 0 WHERE id = ?",
      [defender.id]
    );
    defender.IsAlive = 0;
  }
  
  const attackerKey = `${attacker.user_id || attacker.bot_id}_${isAttackerBot ? 'bot' : 'player'}`;
  if (!totalDamageMap[attackerKey]) totalDamageMap[attackerKey] = 0;
  totalDamageMap[attackerKey] += actualDamage;
  
  let logMessage = '';
  
  if (isEvaded) {
    logMessage = `<span style="color: blue;">${attackerName} атаковал в ${attackZone}, но ${defenderName} увернулся и нанес контрудар ${counterDamage} урона!</span>`;
    
    let counterActualDamage = counterDamage;
    let attackerShield = attacker.shield || 0;
    if (attackerShield > 0) {
      const shieldAbsorb = Math.min(attackerShield, counterActualDamage);
      attackerShield -= shieldAbsorb;
      counterActualDamage -= shieldAbsorb;
      attacker.shield = attackerShield;
      await connection.execute(
        "UPDATE user_battle SET shild = ? WHERE id = ?",
        [attacker.shield, attacker.id]
      );
    }
    
    if (counterActualDamage > 0) {
      const oldAttackerHp = attacker.hp;
      let newAttackerHp = oldAttackerHp - counterActualDamage;
      
      attacker.hp = newAttackerHp;
      await connection.execute(
        "UPDATE user_battle SET hp = ? WHERE id = ?",
        [attacker.hp, attacker.id]
      );
      
      if (attacker.hp <= 0) {
        console.log(`  💀 ${attackerName} УБИТ!`);
        await connection.execute(
          "UPDATE user_battle SET IsAlive = 0 WHERE id = ?",
          [attacker.id]
        );
        attacker.IsAlive = 0;
      }
      
      const defenderKey = `${defender.user_id || defender.bot_id}_${isDefenderBot ? 'bot' : 'player'}`;
      if (!totalDamageMap[defenderKey]) totalDamageMap[defenderKey] = 0;
      totalDamageMap[defenderKey] += counterActualDamage;
    }
  } else if (isCriticalHit) {
    logMessage = `<span style="color: red;">${attackerName} нанес КРИТИЧЕСКИЙ удар в ${attackZone} ${defenderName} и нанес ${actualDamage} урона!</span>`;
  } else if (isBlocked) {
    logMessage = `${attackerName} нанес удар в ${attackZone}, но ${defenderName} заблокировал, урон составил ${actualDamage}`;
  } else {
    logMessage = `${attackerName} нанес удар в ${attackZone} ${defenderName} и нанес ${actualDamage} урона`;
  }
  
  await addBattleLog(connection, battleId, attacker.user_id || attacker.bot_id, defender.user_id || defender.bot_id, logMessage);
  
  return { attacker, defender };
}

// --- Функция проверки и завершения боя (с правильным начислением разницы) ---
// --- Функция проверки и завершения боя (с правильным начислением разницы) ---
// --- Функция проверки и завершения боя (исправленная версия) ---
async function checkAndFinishBattle(connection, battle, battleUsers, totalDamageMap = {}, affectedUsers = new Set()) {
  const [updatedUsers] = await connection.execute(
    "SELECT * FROM user_battle WHERE battle_id = ?",
    [battle.id]
  );
  
  // Загружаем данные по уровням UP
  const [allLevels] = await connection.execute(
    "SELECT * FROM user_levels ORDER BY level ASC, up ASC"
  );
  
  const team1Alive = updatedUsers.some(u => u.komand === 1 && (u.IsAlive === 1 || u.IsAlive === true) && u.hp > 0);
  const team2Alive = updatedUsers.some(u => u.komand === 2 && (u.IsAlive === 1 || u.IsAlive === true) && u.hp > 0);
  
  console.log(`  Battle status - Team1 alive: ${team1Alive}, Team2 alive: ${team2Alive}`);
  
  if (!team1Alive || !team2Alive) {
    console.log(`\n  🏆 BATTLE ${battle.id} FINISHED!`);
    
    for (const battleUser of updatedUsers) {
      if (battleUser.user_id) {
        affectedUsers.add(battleUser.user_id);
      }
    }
    
    for (const battleUser of updatedUsers) {
      if (battleUser.user_id) {
        let totalDamage = battleUser.total_damage || 0;
        
        if (totalDamage === 0) {
          const [damageLogs] = await connection.execute(
            "SELECT SUM(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(log, 'нанес ', -1), ' урона', 1) AS UNSIGNED)) as total FROM battle_log WHERE battle_id = ? AND user_id = ?",
            [battle.id, battleUser.user_id]
          );
          totalDamage = damageLogs[0]?.total || 0;
        }
        
        const isWinner = (team1Alive && battleUser.komand === 1) || (team2Alive && battleUser.komand === 2);
        
        // Получаем текущие данные пользователя
        const [userRows] = await connection.execute(
          "SELECT level, up, exp, points, kr, win, loose FROM user WHERE id = ?",
          [battleUser.user_id]
        );
        
        if (userRows[0]) {
          let currentExp = userRows[0].exp || 0;
          let currentLevel = userRows[0].level || 1;
          let currentUp = userRows[0].up || 0;
          let currentPoints = userRows[0].points || 0;
          let currentKr = userRows[0].kr || 0;
          let currentWin = userRows[0].win || 0;
          let currentLoose = userRows[0].loose || 0;
          
          console.log(`\n  📊 User ${battleUser.user_id} current state:`);
          console.log(`     Level: ${currentLevel}.${currentUp}`);
          console.log(`     Exp: ${currentExp}`);
          console.log(`     Points: ${currentPoints}`);
          console.log(`     Kr: ${currentKr}`);
          
          // Обновляем счетчик побед/поражений
          let newWin = currentWin;
          let newLoose = currentLoose;
          
          if (isWinner) {
            newWin = currentWin + 1;
          } else {
            newLoose = currentLoose + 1;
          }
          
          // Расчет опыта за бой
          let expGain = Math.floor(totalDamage / 10);
          
          if (isWinner) {
            expGain = Math.floor(expGain * 1.5);
          } else {
            expGain = Math.floor(expGain * 0.5);
          }
          expGain = Math.min(expGain, 500);
          if (expGain < 10) expGain = 10;
          
          let newExp = currentExp + expGain;
          
          console.log(`     Exp gain: +${expGain} → ${newExp}`);
          
          // ===== ИСПРАВЛЕННАЯ ЛОГИКА РАСЧЕТА УРОВНЕЙ =====
          // Находим текущий и новый уровни на основе общего опыта
          
          let newLevel = currentLevel;
          let newUp = currentUp;
          let totalPointsBefore = 0;
          let totalPointsAfter = 0;
          let totalKrBefore = 0;
          let totalKrAfter = 0;
          
          // Находим текущие TOTAL значения (до боя)
          for (const levelData of allLevels) {
            if (levelData.level === currentLevel && levelData.up === currentUp) {
              totalPointsBefore = levelData.total_points_to_this_up;
              totalKrBefore = levelData.total_kr_to_this_up;
              break;
            }
          }
          
          // Находим новые уровень и UP на основе нового опыта
          let foundNewLevel = false;
          for (const levelData of allLevels) {
            if (newExp >= levelData.total_exp_to_this_up) {
              newLevel = levelData.level;
              newUp = levelData.up;
              totalPointsAfter = levelData.total_points_to_this_up;
              totalKrAfter = levelData.total_kr_to_this_up;
              foundNewLevel = true;
            } else {
              break;
            }
          }
          
          // Если не нашли подходящий уровень (опыт меньше минимального)
          if (!foundNewLevel && allLevels.length > 0) {
            newLevel = allLevels[0].level;
            newUp = allLevels[0].up;
            totalPointsAfter = allLevels[0].total_points_to_this_up;
            totalKrAfter = allLevels[0].total_kr_to_this_up;
          }
          
          // Начисляем ТОЛЬКО РАЗНИЦУ
          let pointsGained = totalPointsAfter - totalPointsBefore;
          let krGained = totalKrAfter - totalKrBefore;
          
          // Если уровень не изменился, разница будет 0
          if (newLevel === currentLevel && newUp === currentUp) {
            pointsGained = 0;
            krGained = 0;
          }
          
          // Гарантируем, что начисление не отрицательное
          if (pointsGained < 0) pointsGained = 0;
          if (krGained < 0) krGained = 0;
          
          let newPoints = currentPoints + pointsGained;
          let newKr = currentKr + krGained;
          
          console.log(`     Level change: ${currentLevel}.${currentUp} → ${newLevel}.${newUp}`);
          console.log(`     Points gained: +${pointsGained} (${totalPointsBefore} → ${totalPointsAfter})`);
          console.log(`     Kr gained: +${krGained} (${totalKrBefore} → ${totalKrAfter})`);
          
          // Обновляем пользователя
          await connection.execute(
            "UPDATE user SET exp = ?, level = ?, up = ?, points = ?, kr = ?, win = ?, loose = ?, in_battle = NULL, battle_id = NULL WHERE id = ?",
            [newExp, newLevel, newUp, newPoints, newKr, newWin, newLoose, battleUser.user_id]
          );
          
          // Проверяем, что обновление прошло успешно
          const [verifyUser] = await connection.execute(
            "SELECT level, up, exp, points, kr FROM user WHERE id = ?",
            [battleUser.user_id]
          );
          
          console.log(`     VERIFY after update: Level ${verifyUser[0]?.level}.${verifyUser[0]?.up}, Exp ${verifyUser[0]?.exp}, Points ${verifyUser[0]?.points}, Kr ${verifyUser[0]?.kr}`);
          
          const levelUpText = (newLevel > currentLevel || newUp > currentUp) ? ` Повышение до ${newLevel}.${newUp} UP!` : '';
          console.log(`  ✨ User ${battleUser.user_id}: +${expGain} exp, +${pointsGained} points, +${krGained} kr, ${isWinner ? 'WIN' : 'LOOSE'} (${isWinner ? newWin : newLoose}), level ${currentLevel}.${currentUp} → ${newLevel}.${newUp} (total damage: ${totalDamage})`);
          
          // Отправляем приватное сообщение в чат
          const winLoseText = isWinner ? "победой" : "поражением";
          let chatMessage = `Бой закончен ${winLoseText}. +${expGain} опыта, +${pointsGained} очков характеристик, +${krGained} KR.${levelUpText}`;
          
          // Добавляем информацию о новом опыте для отладки
          if (newExp >= 6800 && currentLevel === 1 && currentUp === 0) {
            chatMessage += ` (Опыт: ${currentExp} → ${newExp}, до следующего UP: ${6800 - newExp > 0 ? 6800 - newExp : 0})`;
          }
          
          await sendChatMessage(connection, battleUser.user_id, chatMessage, battle.id);
        }
      }
    }
    
    await connection.execute(
      "UPDATE battle SET started = 2 WHERE id = ?",
      [battle.id]
    );
    
    return true;
  }
  
  return false;
}

// --- Основная функция обработки боев ---
async function processBattles(connection) {
  console.log('\n=== 🔄 Processing battles ===');
  
  try {
    const [activeBattles] = await connection.execute(
      "SELECT * FROM battle WHERE started = 1"
    );
    
    if (activeBattles.length === 0) {
      return;
    }
    
    console.log(`Found ${activeBattles.length} active battles`);
    
    for (const battle of activeBattles) {
      console.log(`\n--- Battle ${battle.id} ---`);
      
      let [battleUsers] = await connection.execute(
        "SELECT * FROM user_battle WHERE battle_id = ?",
        [battle.id]
      );
      
      let [attacks] = await connection.execute(
        "SELECT * FROM battle_attack WHERE battle_id = ? ORDER BY attack_time ASC",
        [battle.id]
      );
      
      console.log(`Participants: ${battleUsers.length}, Attacks: ${attacks.length}`);
      
      if (attacks.length === 0) {
        await updateAllIsAlive(connection, battle.id);
        await checkAndFinishBattle(connection, battle, battleUsers);
        continue;
      }
      
      const hasBot = battleUsers.some(bu => bu.bot_id !== null);
      console.log(`  Battle type: ${hasBot ? 'PvE (Player vs Bot)' : 'PvP (Player vs Player)'}`);
      
      console.log(`  Attacks list:`);
      for (const a of attacks) {
        console.log(`    ID:${a.id} user:${a.user_id} enemy:${a.enemy_id} isBot:${a.isBot} skill:${a.skill || 'none'} time:${a.attack_time}`);
      }
      
      const fullUserData = {};
      for (const battleUser of battleUsers) {
        if (battleUser.user_id && !fullUserData[battleUser.user_id]) {
          const [userRows] = await connection.execute(
            "SELECT * FROM user WHERE id = ?",
            [battleUser.user_id]
          );
          if (userRows[0]) {
            fullUserData[battleUser.user_id] = userRows[0];
            console.log(`  Loaded data for user ID ${battleUser.user_id}: ${userRows[0].username}`);
          }
        }
        if (battleUser.bot_id && !fullUserData[battleUser.bot_id]) {
          const [userRows] = await connection.execute(
            "SELECT * FROM user WHERE id = ?",
            [battleUser.bot_id]
          );
          if (userRows[0]) {
            fullUserData[battleUser.bot_id] = userRows[0];
            console.log(`  Loaded data for bot ID ${battleUser.bot_id}: ${userRows[0].username}`);
          }
        }
      }
      
      const totalDamageMap = {};
      const processedAttackIds = new Set();
      const affectedUsers = new Set();
      
      for (let i = 0; i < attacks.length; i++) {
        const attack = attacks[i];
        if (processedAttackIds.has(attack.id)) continue;
        
        let pairAttack = null;
        for (let j = 0; j < attacks.length; j++) {
          const otherAttack = attacks[j];
          if (otherAttack.id !== attack.id && !processedAttackIds.has(otherAttack.id)) {
            if (otherAttack.user_id === attack.enemy_id && otherAttack.enemy_id === attack.user_id) {
              pairAttack = otherAttack;
              console.log(`  ✅ Found pair: attack ${attack.id} (${attack.user_id}→${attack.enemy_id}, isBot=${attack.isBot}) and attack ${otherAttack.id} (${otherAttack.user_id}→${otherAttack.enemy_id}, isBot=${otherAttack.isBot})`);
              break;
            }
          }
        }
        
        if (pairAttack) {
          console.log(`  🔄 Processing PAIR of attacks`);
          
          // ===== ПЕРВАЯ АТАКА =====
          const isAttacker1Bot = (attack.isBot === 1);
          const attacker1Id = attack.user_id;
          const defender1Id = attack.enemy_id;
          
          let attacker1 = null, defender1 = null;
          let attackerStats1 = null, defenderStats1 = null;
          
          for (const bu of battleUsers) {
            const isAlive = (bu.IsAlive === 1 || bu.IsAlive === true) && bu.hp > 0;
            
            if (isAttacker1Bot) {
              if (bu.bot_id === attacker1Id && isAlive) {
                attacker1 = {
                  id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                  komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                  user_session: bu.user_session, username: fullUserData[attacker1Id]?.username,
                  isBot: true
                };
                attackerStats1 = fullUserData[attacker1Id];
                console.log(`    Attacker1 is BOT: ${attacker1.username || attacker1.bot_id}`);
              }
            } else {
              if (bu.user_id === attacker1Id && isAlive) {
                attacker1 = {
                  id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                  komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                  user_session: bu.user_session, username: fullUserData[attacker1Id]?.username,
                  isBot: false
                };
                attackerStats1 = fullUserData[attacker1Id];
                console.log(`    Attacker1 is PLAYER: ${attacker1.username}`);
              }
            }
          }
          
          for (const bu of battleUsers) {
            const isAlive = (bu.IsAlive === 1 || bu.IsAlive === true) && bu.hp > 0;
            
            if (hasBot) {
              if (isAttacker1Bot) {
                if (bu.user_id === defender1Id && bu.user_id !== null && isAlive) {
                  defender1 = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[defender1Id]?.username,
                    isBot: false
                  };
                  defenderStats1 = fullUserData[defender1Id];
                  console.log(`    Defender1 is PLAYER: ${defender1.username}`);
                }
              } else {
                if (bu.bot_id === defender1Id && bu.bot_id !== null && isAlive) {
                  defender1 = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[defender1Id]?.username,
                    isBot: true
                  };
                  defenderStats1 = fullUserData[defender1Id];
                  console.log(`    Defender1 is BOT: ${defender1.username || defender1.bot_id}`);
                }
              }
            } else {
              if (bu.user_id === defender1Id && isAlive) {
                defender1 = {
                  id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                  komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                  user_session: bu.user_session, username: fullUserData[defender1Id]?.username,
                  isBot: false
                };
                defenderStats1 = fullUserData[defender1Id];
                console.log(`    Defender1 is PLAYER: ${defender1.username}`);
              }
            }
          }
          
          // ===== ВТОРАЯ АТАКА =====
          const isAttacker2Bot = (pairAttack.isBot === 1);
          const attacker2Id = pairAttack.user_id;
          const defender2Id = pairAttack.enemy_id;
          
          let attacker2 = null, defender2 = null;
          let attackerStats2 = null, defenderStats2 = null;
          
          for (const bu of battleUsers) {
            const isAlive = (bu.IsAlive === 1 || bu.IsAlive === true) && bu.hp > 0;
            
            if (isAttacker2Bot) {
              if (bu.bot_id === attacker2Id && isAlive) {
                attacker2 = {
                  id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                  komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                  user_session: bu.user_session, username: fullUserData[attacker2Id]?.username,
                  isBot: true
                };
                attackerStats2 = fullUserData[attacker2Id];
                console.log(`    Attacker2 is BOT: ${attacker2.username || attacker2.bot_id}`);
              }
            } else {
              if (bu.user_id === attacker2Id && isAlive) {
                attacker2 = {
                  id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                  komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                  user_session: bu.user_session, username: fullUserData[attacker2Id]?.username,
                  isBot: false
                };
                attackerStats2 = fullUserData[attacker2Id];
                console.log(`    Attacker2 is PLAYER: ${attacker2.username}`);
              }
            }
          }
          
          for (const bu of battleUsers) {
            const isAlive = (bu.IsAlive === 1 || bu.IsAlive === true) && bu.hp > 0;
            
            if (hasBot) {
              if (isAttacker2Bot) {
                if (bu.user_id === defender2Id && bu.user_id !== null && isAlive) {
                  defender2 = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[defender2Id]?.username,
                    isBot: false
                  };
                  defenderStats2 = fullUserData[defender2Id];
                  console.log(`    Defender2 is PLAYER: ${defender2.username}`);
                }
              } else {
                if (bu.bot_id === defender2Id && bu.bot_id !== null && isAlive) {
                  defender2 = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[defender2Id]?.username,
                    isBot: true
                  };
                  defenderStats2 = fullUserData[defender2Id];
                  console.log(`    Defender2 is BOT: ${defender2.username || defender2.bot_id}`);
                }
              }
            } else {
              if (bu.user_id === defender2Id && isAlive) {
                defender2 = {
                  id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                  komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                  user_session: bu.user_session, username: fullUserData[defender2Id]?.username,
                  isBot: false
                };
                defenderStats2 = fullUserData[defender2Id];
                console.log(`    Defender2 is PLAYER: ${defender2.username}`);
              }
            }
          }
          
          if (attacker1 && attacker1.user_id) affectedUsers.add(attacker1.user_id);
          if (defender1 && defender1.user_id) affectedUsers.add(defender1.user_id);
          if (attacker2 && attacker2.user_id) affectedUsers.add(attacker2.user_id);
          if (defender2 && defender2.user_id) affectedUsers.add(defender2.user_id);
          
          if (attacker1 && defender1 && attackerStats1 && defenderStats1 && attacker1.hp > 0 && defender1.hp > 0) {
            console.log(`  ⚔️ Processing attack 1: ${attacker1.isBot ? '[BOT]' : '[PLAYER]'} → ${defender1.isBot ? '[BOT]' : '[PLAYER]'}`);
            await processNormalAttack(connection, attack, attacker1, defender1, battle.id, totalDamageMap, attackerStats1, defenderStats1, fullUserData, battleUsers);
          } else {
            console.log(`  ⚠️ Cannot process attack 1: attacker1=${!!attacker1}, defender1=${!!defender1}`);
          }
          
          if (defender1 && defender1.id) {
            const [updated] = await connection.execute(
              "SELECT * FROM user_battle WHERE id = ?",
              [defender1.id]
            );
            if (updated[0]) {
              defender1.hp = updated[0].hp;
              defender1.shield = updated[0].shild;
              defender1.IsAlive = updated[0].IsAlive;
            }
          }
          
          if (defender2 && defender2.id) {
            const [updatedBot] = await connection.execute(
              "SELECT * FROM user_battle WHERE id = ?",
              [defender2.id]
            );
            if (updatedBot[0]) {
              defender2.hp = updatedBot[0].hp;
              defender2.shield = updatedBot[0].shild;
              defender2.IsAlive = updatedBot[0].IsAlive;
            }
          }
          
          if (attacker2 && defender2 && attackerStats2 && defenderStats2 && defender2.hp > 0 && defender2.IsAlive === 1 && attacker2.hp > 0) {
            console.log(`  ⚔️ Processing attack 2: ${attacker2.isBot ? '[BOT]' : '[PLAYER]'} → ${defender2.isBot ? '[BOT]' : '[PLAYER]'}`);
            await processNormalAttack(connection, pairAttack, attacker2, defender2, battle.id, totalDamageMap, attackerStats2, defenderStats2, fullUserData, battleUsers);
          } else if (attacker2 && defender2) {
            console.log(`  ⚠️ Cannot process attack 2: defender dead (hp=${defender2.hp}, alive=${defender2.IsAlive})`);
          }
          
          processedAttackIds.add(attack.id);
          processedAttackIds.add(pairAttack.id);
          
        } else {
          const currentTimeSeconds = Math.floor(Date.now() / 1000);
          const timeDiffSeconds = currentTimeSeconds - (attack.attack_time || 0);
          
          console.log(`  No pair found for attack ${attack.id}, age: ${timeDiffSeconds}s`);
          
          if (timeDiffSeconds > 30) {
            console.log(`  ⏰ Processing SINGLE attack (timeout ${timeDiffSeconds}s)`);
            
            const isAttackerBot = (attack.isBot === 1);
            const attackerId = attack.user_id;
            const defenderId = attack.enemy_id;
            
            let attacker = null, defender = null;
            let attackerStats = null, defenderStats = null;
            
            for (const bu of battleUsers) {
              const isAlive = (bu.IsAlive === 1 || bu.IsAlive === true) && bu.hp > 0;
              
              if (isAttackerBot) {
                if (bu.bot_id === attackerId && isAlive) {
                  attacker = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[attackerId]?.username,
                    isBot: true
                  };
                  attackerStats = fullUserData[attackerId];
                  console.log(`    Attacker is BOT`);
                }
              } else {
                if (bu.user_id === attackerId && isAlive) {
                  attacker = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[attackerId]?.username,
                    isBot: false
                  };
                  attackerStats = fullUserData[attackerId];
                  console.log(`    Attacker is PLAYER`);
                }
              }
            }
            
            for (const bu of battleUsers) {
              const isAlive = (bu.IsAlive === 1 || bu.IsAlive === true) && bu.hp > 0;
              
              if (hasBot) {
                if (!isAttackerBot) {
                  if (bu.bot_id === defenderId && bu.bot_id !== null && isAlive) {
                    defender = {
                      id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                      komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                      user_session: bu.user_session, username: fullUserData[defenderId]?.username,
                      isBot: true
                    };
                    defenderStats = fullUserData[defenderId];
                    console.log(`    Defender is BOT`);
                  }
                } else {
                  if (bu.user_id === defenderId && bu.user_id !== null && isAlive) {
                    defender = {
                      id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                      komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                      user_session: bu.user_session, username: fullUserData[defenderId]?.username,
                      isBot: false
                    };
                    defenderStats = fullUserData[defenderId];
                    console.log(`    Defender is PLAYER`);
                  }
                }
              } else {
                if (bu.user_id === defenderId && isAlive) {
                  defender = {
                    id: bu.id, hp: bu.hp, shield: bu.shild || 0, IsAlive: bu.IsAlive,
                    komand: bu.komand, user_id: bu.user_id, bot_id: bu.bot_id,
                    user_session: bu.user_session, username: fullUserData[defenderId]?.username,
                    isBot: false
                  };
                  defenderStats = fullUserData[defenderId];
                  console.log(`    Defender is PLAYER`);
                }
              }
            }
            
            if (attacker && defender && attackerStats && defenderStats && attacker.hp > 0 && defender.hp > 0) {
              if (attacker.user_id) affectedUsers.add(attacker.user_id);
              if (defender.user_id) affectedUsers.add(defender.user_id);
              
              attack.block = 0;
              console.log(`  ⚔️ Processing single attack: ${attacker.isBot ? '[BOT]' : '[PLAYER]'} → ${defender.isBot ? '[BOT]' : '[PLAYER]'} (no block)`);
              await processNormalAttack(connection, attack, attacker, defender, battle.id, totalDamageMap, attackerStats, defenderStats, fullUserData, battleUsers);
            }
            
            processedAttackIds.add(attack.id);
          }
        }
      }
      
      // Сначала обновляем total_damage в БД
      console.log(`  Updating total_damage records before finish check:`, totalDamageMap);
      for (const [key, damage] of Object.entries(totalDamageMap)) {
        const [userId, type] = key.split('_');
        
        if (type === 'player') {
          console.log(`    Updating player user_id=${userId}: +${damage}`);
          await connection.execute(
            "UPDATE user_battle SET total_damage = COALESCE(total_damage, 0) + ? WHERE user_id = ? AND battle_id = ?",
            [damage, userId, battle.id]
          );
        } else if (type === 'bot') {
          console.log(`    Updating bot bot_id=${userId}: +${damage}`);
          await connection.execute(
            "UPDATE user_battle SET total_damage = COALESCE(total_damage, 0) + ? WHERE bot_id = ? AND battle_id = ?",
            [damage, userId, battle.id]
          );
        }
      }
      
      await updateAllIsAlive(connection, battle.id);
      
      const battleFinished = await checkAndFinishBattle(connection, battle, battleUsers, totalDamageMap, affectedUsers);
      
      if (battleFinished) {
        const [allParticipants] = await connection.execute(
          "SELECT * FROM user_battle WHERE battle_id = ? AND user_id IS NOT NULL",
          [battle.id]
        );
        
        for (const bu of allParticipants) {
          if (bu.user_session) {
            const ws = users[bu.user_session];
            if (ws && ws.readyState === WebSocket.OPEN) {
              ws.send(JSON.stringify({ type: 'command', command: 'reload' }));
              console.log(`  📤 Sent reload to user ${bu.user_id} (battle finished)`);
            }
          }
        }
      }
      
      if (processedAttackIds.size > 0) {
        const idsToDelete = Array.from(processedAttackIds);
        await connection.execute(
          `DELETE FROM battle_attack WHERE id IN (${idsToDelete.map(() => '?').join(',')})`,
          idsToDelete
        );
        console.log(`  🗑️ Deleted ${idsToDelete.length} processed attacks`);
      }
      
      if (!battleFinished && affectedUsers.size > 0) {
        console.log(`\n  📤 Sending reload to ${affectedUsers.size} affected users:`);
        for (const userId of affectedUsers) {
          const userBattle = battleUsers.find(bu => bu.user_id === userId);
          if (userBattle && userBattle.user_session) {
            const ws = users[userBattle.user_session];
            if (ws && ws.readyState === WebSocket.OPEN) {
              ws.send(JSON.stringify({ type: 'command', command: 'reload' }));
              console.log(`    ✅ Sent reload to user ${userId} (session: ${userBattle.user_session})`);
            } else {
              console.log(`    ⚠️ WebSocket not available for user ${userId}, state=${ws?.readyState}`);
            }
          }
        }
      }
    }
    
  } catch (error) {
    console.error('❌ Error processing battles:', error);
    console.error(error.stack);
  }
}

// --- Главная функция запуска сервера ---
async function startServer() {
  let connection;
  try {
    connection = await mysql.createConnection(dbConfig);
    console.log('✅ Connected to MySQL');

    const app = express();
    const server = http.createServer(app);
    const wss = new WebSocket.Server({ server });

    wss.on('connection', (ws, req) => {
      const userId = req.url.split('?')[1]?.split('=')[1] ?? uuidv4();
      console.log(`🔌 WebSocket connected: user ${userId}`);

      if (users[userId] && users[userId].readyState === WebSocket.OPEN) {
        users[userId].terminate();
      }

      users[userId] = ws;
      ws.send(JSON.stringify({ type: 'welcome', message: `Connected as ${userId}` }));

      ws.on('close', () => {
        delete users[userId];
        console.log(`🔌 WebSocket disconnected: user ${userId}`);
      });
    });

    setInterval(async () => {
      console.log('\n--- 🎮 Checking for battles to start ---');
      try {
        const currentTimeSeconds = Math.floor(Date.now() / 1000);
        const [battles] = await connection.execute(
          "SELECT * FROM battle WHERE started IS NULL AND start_time < ?",
          [currentTimeSeconds]
        );
        
        if (battles.length === 0) return;
        
        console.log(`Found ${battles.length} battles to start`);
        
        for (const battle of battles) {
          console.log(`\n  Starting battle ${battle.id}`);
          await connection.execute("UPDATE battle SET started = 1 WHERE id = ?", [battle.id]);
          
          let [participants] = await connection.execute(
            "SELECT * FROM user_battle WHERE battle_id = ?",
            [battle.id]
          );
          
          if (participants.length === 1) {
            const realUserBattle = participants[0];
            const [userDataRows] = await connection.execute(
              "SELECT health FROM user WHERE id = ?",
              [realUserBattle.user_id]
            );
            
            if (userDataRows.length > 0) {
              const userData = userDataRows[0];
              console.log(`  Adding bot for user ${realUserBattle.user_id}`);
              await connection.execute(
                "INSERT INTO user_battle (bot_id, battle_id, hp, komand, IsAlive, shild, total_damage) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [realUserBattle.user_id, battle.id, userData.health, 2, 1, 0, 0]
              );
              const [updated] = await connection.execute(
                "SELECT * FROM user_battle WHERE battle_id = ?",
                [battle.id]
              );
              participants = updated;
            }
          }
          
          let team1Priority = 1, team2Priority = 1;
          const halfCount = Math.ceil(participants.length / 2);
          
          for (let i = 0; i < participants.length; i++) {
            const participant = participants[i];
            const team = i < halfCount ? 1 : 2;
            const priority = team === 1 ? team1Priority++ : team2Priority++;
            
            await connection.execute(
              "UPDATE user_battle SET komand = ?, priority = ? WHERE id = ?",
              [team, priority, participant.id]
            );
            
            if (participant.user_id) {
              await connection.execute(
                "UPDATE user SET in_battle = 1, battle_id = ? WHERE id = ?",
                [battle.id, participant.user_id]
              );
              
              const ws = users[participant.user_session];
              if (ws && ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({ type: 'command', command: 'reload' }));
                console.log(`  📤 Sent reload to user ${participant.user_id}`);
              }
            }
          }
          
          console.log(`  ✅ Battle ${battle.id} started successfully`);
        }
      } catch (error) {
        console.error('❌ Error starting battles:', error);
      }
    }, 5000);
    
    setInterval(async () => {
      await processBattles(connection);
    }, 5000);
    
    const PORT = process.env.PORT || 8080;
    server.listen(PORT, () => {
      console.log(`\n🚀 Server running on http://localhost:${PORT}`);
      console.log(`🔌 WebSocket: ws://localhost:${PORT}`);
      console.log(`\n⚔️ Battle system ready!\n`);
    });
    
  } catch (error) {
    console.error('❌ Fatal error:', error);
    process.exit(1);
  }
}

startServer();
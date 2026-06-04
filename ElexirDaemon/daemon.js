const mysql = require('mysql2/promise');

class ElixirDaemon {
    constructor() {
        this.dbConfig = {
            host: 'localhost',
            port: 3306,
            user: 'root',
            password: '',
            database: 'combats',
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0
        };
        
        this.pool = null;
        this.isRunning = true;
    }

    async initialize() {
        this.pool = mysql.createPool(this.dbConfig);
        console.log('[DAEMON] Инициализирован');
        console.log('[DAEMON] Подключение к БД:', this.dbConfig.host, this.dbConfig.database);
        
        // Диагностика при запуске
        await this.diagnose();
    }
    
    async diagnose() {
        let connection;
        try {
            connection = await this.pool.getConnection();
            
            console.log('\n[DIAGNOSE] ===== ДИАГНОСТИКА =====');
            
            // Проверяем пользователя 2
            const [users] = await connection.query(
                'SELECT id, username, str, dex, inte, endu, weapon FROM user WHERE id = 2'
            );
            
            if (users.length > 0) {
                console.log(`[DIAGNOSE] Пользователь ID=${users[0].id}, weapon=${users[0].weapon}`);
                console.log(`[DIAGNOSE] Характеристики: STR=${users[0].str}, DEX=${users[0].dex}, INT=${users[0].inte}, ENDU=${users[0].endu}`);
            }
            
            // Проверяем предмет в слоте weapon
            const [items] = await connection.query(
                'SELECT * FROM inventory WHERE id = (SELECT weapon FROM user WHERE id = 2)'
            );
            
            if (items.length > 0) {
                console.log(`[DIAGNOSE] Предмет в слоте weapon: ID=${items[0].id}, name=${items[0].name}`);
                console.log(`[DIAGNOSE] Бонусы предмета: str=${items[0].str}, dex=${items[0].dex}, end=${items[0].end}, damage=${items[0].damage}`);
            }
            
            console.log('[DIAGNOSE] =========================\n');
            
        } catch (error) {
            console.error('[DIAGNOSE] Ошибка:', error);
        } finally {
            if (connection) connection.release();
        }
    }

    async checkRequirements(item, user) {
        console.log(`\n[CHECK] --- Проверка предмета ${item.id} (${item.name}) ---`);
        console.log(`[CHECK] Требования предмета:`);
        console.log(`[CHECK]   n_str: ${item.n_str || 0}, У пользователя str: ${user.str || 0}`);
        console.log(`[CHECK]   n_dex: ${item.n_dex || 0}, У пользователя dex: ${user.dex || 0}`);
        console.log(`[CHECK]   n_inte: ${item.n_inte || 0}, У пользователя inte: ${user.inte || 0}`);
        console.log(`[CHECK]   n_intu: ${item.n_intu || 0}, У пользователя intu: ${user.intu || 0}`);
        console.log(`[CHECK]   n_end: ${item.n_end || 0}, У пользователя endu: ${user.endu || 0}`);
        console.log(`[CHECK]   n_level: ${item.n_level || 0}, У пользователя level: ${user.level || 0}`);
        
        let failed = false;
        
        if ((user.str || 0) < (item.n_str || 0)) {
            console.log(`[CHECK] ❌ НЕ ХВАТАЕТ STR: ${user.str || 0} < ${item.n_str}`);
            failed = true;
        }
        if ((user.dex || 0) < (item.n_dex || 0)) {
            console.log(`[CHECK] ❌ НЕ ХВАТАЕТ DEX: ${user.dex || 0} < ${item.n_dex}`);
            failed = true;
        }
        if ((user.inte || 0) < (item.n_inte || 0)) {
            console.log(`[CHECK] ❌ НЕ ХВАТАЕТ INT: ${user.inte || 0} < ${item.n_inte}`);
            failed = true;
        }
        if ((user.intu || 0) < (item.n_intu || 0)) {
            console.log(`[CHECK] ❌ НЕ ХВАТАЕТ INTU: ${user.intu || 0} < ${item.n_intu}`);
            failed = true;
        }
        if ((user.endu || 0) < (item.n_end || 0)) {
            console.log(`[CHECK] ❌ НЕ ХВАТАЕТ END: ${user.endu || 0} < ${item.n_end}`);
            failed = true;
        }
        if ((user.level || 0) < (item.n_level || 0)) {
            console.log(`[CHECK] ❌ НЕ ХВАТАЕТ LEVEL: ${user.level || 0} < ${item.n_level}`);
            failed = true;
        }
        
        if (!failed) {
            console.log(`[CHECK] ✅ Все требования соблюдены`);
            return true;
        } else {
            console.log(`[CHECK] ❌ Требования НЕ соблюдены - нужно снять предмет`);
            return false;
        }
    }

    async undressItem(userId, slot, itemId, connection) {
        try {
            console.log(`\n[UNDRESS] ===== СНИМАЕМ ПРЕДМЕТ =====`);
            console.log(`[UNDRESS] userId: ${userId}, slot: ${slot}, itemId: ${itemId}`);

            // Получаем информацию об item
            const [items] = await connection.query(
                'SELECT * FROM inventory WHERE id = ?',
                [itemId]
            );

            if (items.length === 0) {
                console.log(`[UNDRESS] ❌ Item ${itemId} не найден в inventory!`);
                console.log(`[UNDRESS] Очищаем слот ${slot} без снятия бонусов`);
                
                await connection.query(
                    `UPDATE user SET ${slot} = NULL WHERE id = ?`,
                    [userId]
                );
                console.log(`[UNDRESS] ✅ Слот ${slot} очищен`);
                return;
            }

            const item = items[0];
            console.log(`[UNDRESS] Предмет: ${item.name}`);
            console.log(`[UNDRESS] Текущий dressed: ${item.dressed || 0}`);

            // Получаем пользователя
            const [users] = await connection.query(
                'SELECT * FROM user WHERE id = ?',
                [userId]
            );

            if (users.length === 0) {
                console.log(`[UNDRESS] ❌ Пользователь ${userId} не найден`);
                return;
            }
            
            const user = users[0];
            console.log(`[UNDRESS] Характеристики ДО снятия: STR=${user.str || 0}, DEX=${user.dex || 0}, INT=${user.inte || 0}, ENDU=${user.endu || 0}`);

            // Отнимаем бонусы предмета (обрабатываем NULL значения)
            const stats = [
                { name: 'str', value: item.str },
                { name: 'dex', value: item.dex },
                { name: 'inte', value: item.inte },
                { name: 'intu', value: item.intu },
                { name: 'endu', value: item.end },
                { name: 'fire', value: item.fire },
                { name: 'water', value: item.water },
                { name: 'air', value: item.air },
                { name: 'earth', value: item.earth },
                { name: 'damage', value: item.damage },
                { name: 'defence', value: item.defence },
                { name: 'health', value: item.health },
                { name: 'mana', value: item.mana },
                { name: 'crit', value: item.crit },
                { name: 'anticrit', value: item.anticrit },
                { name: 'mdef', value: item.mdef },
                { name: 'evaision', value: item.evaision },
                { name: 'aeveision', value: item.aeveision }
            ];
            
            const updateFields = [];
            const updateValues = [];

            for (const stat of stats) {
                const statValue = stat.value !== null && stat.value !== undefined ? stat.value : 0;
                if (statValue !== 0) {
                    // Используем IFNULL для обработки NULL значений в базе
                    updateFields.push(`${stat.name} = IFNULL(${stat.name}, 0) - ?`);
                    updateValues.push(statValue);
                    console.log(`[UNDRESS] Отнимаем ${statValue} ${stat.name}`);
                }
            }

            if (updateFields.length > 0) {
                const query = `UPDATE user SET ${updateFields.join(', ')} WHERE id = ?`;
                updateValues.push(userId);
                console.log(`[UNDRESS] SQL: ${query}`);
                console.log(`[UNDRESS] Values: ${updateValues.join(', ')}`);
                await connection.query(query, updateValues);
                console.log(`[UNDRESS] ✅ Бонусы сняты`);
            }

            // Очищаем слот у пользователя
            const [slotResult] = await connection.query(
                `UPDATE user SET ${slot} = NULL WHERE id = ?`,
                [userId]
            );
            console.log(`[UNDRESS] ✅ Слот ${slot} очищен (affectedRows: ${slotResult.affectedRows})`);

            // Обновляем dressed в inventory
            const [dressedResult] = await connection.query(
                'UPDATE inventory SET dressed = 0 WHERE id = ?',
                [itemId]
            );
            console.log(`[UNDRESS] ✅ Обновление dressed: affectedRows = ${dressedResult.affectedRows}`);

            // Проверяем результат
            const [checkItem] = await connection.query(
                'SELECT dressed FROM inventory WHERE id = ?',
                [itemId]
            );
            
            if (checkItem.length > 0) {
                console.log(`[UNDRESS] ✅ Проверка: dressed теперь = ${checkItem[0].dressed || 0}`);
            }
            
            // Получаем финальные характеристики
            const [finalUser] = await connection.query(
                'SELECT str, dex, inte, endu FROM user WHERE id = ?',
                [userId]
            );
            
            if (finalUser.length > 0) {
                console.log(`[UNDRESS] Характеристики ПОСЛЕ снятия: STR=${finalUser[0].str || 0}, DEX=${finalUser[0].dex || 0}, INT=${finalUser[0].inte || 0}, ENDU=${finalUser[0].endu || 0}`);
            }
            
            console.log(`[UNDRESS] ===== ПРЕДМЕТ УСПЕШНО СНЯТ =====\n`);

        } catch (error) {
            console.error(`[UNDRESS] ❌ Ошибка:`, error);
        }
    }

    async checkAndUnequipItems(userId, connection) {
        try {
            console.log(`\n[CHECK] ===== ПРОВЕРКА ЭКИПИРОВКИ ПОЛЬЗОВАТЕЛЯ ${userId} =====`);
            
            // Получаем пользователя
            const [users] = await connection.query(
                'SELECT * FROM user WHERE id = ?',
                [userId]
            );

            if (users.length === 0) {
                console.log(`[CHECK] ❌ Пользователь ${userId} не найден`);
                return;
            }
            
            const user = users[0];
            console.log(`[CHECK] Текущие характеристики: STR=${user.str || 0}, DEX=${user.dex || 0}, INT=${user.inte || 0}, INTU=${user.intu || 0}, END=${user.endu || 0}, LEVEL=${user.level || 0}`);

            // Слоты для проверки
            const slots = ['helm', 'weapon', 'shild', 'chest', 'leg', 'brasers', 'belt', 
                          'gloves', 'boots', 'earrings', 'amulet', 'ring1', 'ring2', 'ring3'];

            for (const slot of slots) {
                const itemId = user[slot];
                
                if (itemId && itemId > 0) {
                    console.log(`\n[CHECK] --- Проверяем слот ${slot} (itemId: ${itemId}) ---`);
                    
                    const [items] = await connection.query(
                        'SELECT * FROM inventory WHERE id = ?',
                        [itemId]
                    );

                    if (items.length > 0) {
                        const item = items[0];
                        console.log(`[CHECK] Название предмета: ${item.name}`);
                        console.log(`[CHECK] Текущий статус dressed: ${item.dressed || 0}`);
                        
                        const meetsRequirements = await this.checkRequirements(item, user);
                        
                        if (!meetsRequirements) {
                            console.log(`[CHECK] 🔴 ПРЕДМЕТ ДОЛЖЕН БЫТЬ СНЯТ!`);
                            await this.undressItem(userId, slot, itemId, connection);
                        } else {
                            console.log(`[CHECK] 🟢 Предмет подходит по требованиям`);
                        }
                    } else {
                        console.log(`[CHECK] ❌ Предмет ${itemId} не найден в inventory!`);
                        console.log(`[CHECK] Очищаем слот ${slot}...`);
                        await connection.query(
                            `UPDATE user SET ${slot} = NULL WHERE id = ?`,
                            [userId]
                        );
                        console.log(`[CHECK] ✅ Слот ${slot} очищен`);
                    }
                }
            }
            
            console.log(`[CHECK] ===== ПРОВЕРКА ЗАВЕРШЕНА =====\n`);

        } catch (error) {
            console.error(`[CHECK] ❌ Ошибка:`, error);
        }
    }

    async processExpiredElixirs() {
        let connection;
        
        try {
            connection = await this.pool.getConnection();
            await connection.beginTransaction();

            const now = Math.floor(Date.now() / 1000);
            
            const [expiredElixirs] = await connection.query(
                'SELECT * FROM user_elexir WHERE use_time < ?',
                [now]
            );

            if (expiredElixirs.length > 0) {
                console.log(`\n[DAEMON] ===== НАЙДЕНО ${expiredElixirs.length} ИСТЕКШИХ ЭЛИКСИРОВ =====`);
                
                for (const elixir of expiredElixirs) {
                    console.log(`\n[PROCESS] --- Обработка эликсира ${elixir.id} ---`);
                    console.log(`[PROCESS] Пользователь: ${elixir.user_id}`);
                    console.log(`[PROCESS] Тип: ${elixir.type}`);
                    
                    switch (elixir.type) {
                        case 'str':
                            await connection.query(
                                'UPDATE user SET str = IFNULL(str, 0) - 10 WHERE id = ?',
                                [elixir.user_id]
                            );
                            console.log(`[PROCESS] ✅ Отнято 10 STR`);
                            break;
                        case 'intu':
                            await connection.query(
                                'UPDATE user SET intu = IFNULL(intu, 0) - 10 WHERE id = ?',
                                [elixir.user_id]
                            );
                            console.log(`[PROCESS] ✅ Отнято 10 INTU`);
                            break;
                        case 'inte':
                            await connection.query(
                                'UPDATE user SET inte = IFNULL(inte, 0) - 10 WHERE id = ?',
                                [elixir.user_id]
                            );
                            console.log(`[PROCESS] ✅ Отнято 10 INTE`);
                            break;
                        case 'dex':
                            await connection.query(
                                'UPDATE user SET dex = IFNULL(dex, 0) - 10 WHERE id = ?',
                                [elixir.user_id]
                            );
                            console.log(`[PROCESS] ✅ Отнято 10 DEX`);
                            break;
                        default:
                            console.log(`[PROCESS] ⚠️ Неизвестный тип: ${elixir.type}`);
                    }
                    
                    await connection.query(
                        'DELETE FROM user_elexir WHERE id = ?',
                        [elixir.id]
                    );
                    console.log(`[PROCESS] ✅ Эликсир удален`);
                }
                
                const uniqueUsers = [...new Set(expiredElixirs.map(e => e.user_id))];
                console.log(`\n[DAEMON] Уникальных пользователей: ${uniqueUsers.length}`);
                
                for (const userId of uniqueUsers) {
                    await this.checkAndUnequipItems(userId, connection);
                }
            }
            
            await connection.commit();
            console.log(`[DAEMON] ✅ Транзакция закоммичена\n`);
            
        } catch (error) {
            if (connection) {
                await connection.rollback();
                console.log(`[DAEMON] ❌ Транзакция откачена`);
            }
            console.error('[DAEMON] Ошибка:', error);
        } finally {
            if (connection) {
                connection.release();
            }
        }
    }

    async start() {
        await this.initialize();
        
        console.log('[DAEMON] Запущен. Интервал проверки: 5 секунд');
        console.log('[DAEMON] Для остановки нажмите Ctrl+C\n');
        
        setInterval(async () => {
            if (!this.isRunning) return;
            await this.processExpiredElixirs();
        }, 5000);
        
        process.on('SIGINT', async () => {
            console.log('\n[DAEMON] Завершение...');
            this.isRunning = false;
            if (this.pool) await this.pool.end();
            console.log('[DAEMON] Завершен');
            process.exit(0);
        });
    }
}

const daemon = new ElixirDaemon();
daemon.start().catch(error => {
    console.error('[DAEMON] Критическая ошибка:', error);
    process.exit(1);
});
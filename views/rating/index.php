<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Рейтинг';

$currentSort = $sort ?? 'exp';
$currentOrder = $order ?? 'desc';
$currentClanSort = $clanSort ?? 'total_exp';
$currentClanOrder = $clanOrder ?? 'desc';
?>

<div class="rating-container">
    <div class="rating-header">
        <h1>Рейтинг</h1>
        <p class="subtitle">Лучшие игроки и кланы</p>
    </div>

    <!-- Вкладки -->
    <div class="rating-tabs">
        <button class="tab-btn active" data-tab="players">Игроки</button>
        <button class="tab-btn" data-tab="clans">Кланы</button>
    </div>

    <!-- Вкладка Игроки -->
    <div id="tab-players" class="tab-content active">
        <div class="rating-table-container">
            <table class="rating-table">
                <thead>
                    <tr>
                        <th class="col-rank">#</th>
                        <th class="col-name sortable" data-sort="username" data-order="<?= $currentSort == 'username' && $currentOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Игрок
                            <?php if ($currentSort == 'username'): ?>
                                <span class="sort-icon <?= $currentOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-level sortable" data-sort="level" data-order="<?= $currentSort == 'level' && $currentOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Уровень
                            <?php if ($currentSort == 'level'): ?>
                                <span class="sort-icon <?= $currentOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-exp sortable" data-sort="exp" data-order="<?= $currentSort == 'exp' && $currentOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Опыт
                            <?php if ($currentSort == 'exp'): ?>
                                <span class="sort-icon <?= $currentOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-repa sortable" data-sort="repa" data-order="<?= $currentSort == 'repa' && $currentOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Репутация
                            <?php if ($currentSort == 'repa'): ?>
                                <span class="sort-icon <?= $currentOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-win sortable" data-sort="win" data-order="<?= $currentSort == 'win' && $currentOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Победы
                            <?php if ($currentSort == 'win'): ?>
                                <span class="sort-icon <?= $currentOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-kr sortable" data-sort="kr" data-order="<?= $currentSort == 'kr' && $currentOrder == 'asc' ? 'desc' : 'asc' ?>">
                            KR
                            <?php if ($currentSort == 'kr'): ?>
                                <span class="sort-icon <?= $currentOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody id="players-list">
                    <tr><td colspan="7" class="loading">Загрузка...</td></tr>
                </tbody>
            </table>
            <div id="players-pagination" class="pagination-container"></div>
        </div>
    </div>

    <!-- Вкладка Кланы -->
    <div id="tab-clans" class="tab-content">
        <div class="rating-table-container">
            <table class="rating-table">
                <thead>
                    <tr>
                        <th class="col-rank">#</th>
                        <th class="col-clan-name sortable" data-sort="name" data-order="<?= $currentClanSort == 'name' && $currentClanOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Название
                            <?php if ($currentClanSort == 'name'): ?>
                                <span class="sort-icon <?= $currentClanOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-members sortable" data-sort="members_count" data-order="<?= $currentClanSort == 'members_count' && $currentClanOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Участников
                            <?php if ($currentClanSort == 'members_count'): ?>
                                <span class="sort-icon <?= $currentClanOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-clan-exp sortable" data-sort="total_exp" data-order="<?= $currentClanSort == 'total_exp' && $currentClanOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Общий опыт
                            <?php if ($currentClanSort == 'total_exp'): ?>
                                <span class="sort-icon <?= $currentClanOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-clan-repa sortable" data-sort="total_repa" data-order="<?= $currentClanSort == 'total_repa' && $currentClanOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Общая репутация
                            <?php if ($currentClanSort == 'total_repa'): ?>
                                <span class="sort-icon <?= $currentClanOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-clan-win sortable" data-sort="total_win" data-order="<?= $currentClanSort == 'total_win' && $currentClanOrder == 'asc' ? 'desc' : 'asc' ?>">
                            Всего побед
                            <?php if ($currentClanSort == 'total_win'): ?>
                                <span class="sort-icon <?= $currentClanOrder == 'asc' ? 'asc' : 'desc' ?>">▲</span>
                            <?php endif; ?>
                        </th>
                        <th class="col-clan-master">Глава</th>
                    </tr>
                </thead>
                <tbody id="clans-list">
                    <tr><td colspan="7" class="loading">Загрузка...</td></tr>
                </tbody>
            </table>
            <div id="clans-pagination" class="pagination-container"></div>
        </div>
    </div>
</div>

<script>
// Переключение вкладок
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        this.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
        
        if (tabName === 'players') {
            loadPlayers();
        } else {
            loadClans();
        }
    });
});

// Загрузка игроков
function loadPlayers(page = 1) {
    const sort = getSortParam();
    const order = getOrderParam();
    
    fetch('/rating/players?sort=' + sort + '&order=' + order + '&page=' + page, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('players-list').innerHTML = html;
    });
}

// Загрузка кланов
function loadClans(page = 1) {
    const sort = getClanSortParam();
    const order = getClanOrderParam();
    
    fetch('/rating/clans?sort=' + sort + '&order=' + order + '&page=' + page, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('clans-list').innerHTML = html;
    });
}

function getSortParam() {
    return '<?= $currentSort ?>';
}

function getOrderParam() {
    return '<?= $currentOrder ?>';
}

function getClanSortParam() {
    return '<?= $currentClanSort ?>';
}

function getClanOrderParam() {
    return '<?= $currentClanOrder ?>';
}

// Сортировка для игроков
document.querySelectorAll('#tab-players .sortable').forEach(el => {
    el.addEventListener('click', function() {
        const sort = this.getAttribute('data-sort');
        let order = this.getAttribute('data-order');
        
        window.location.href = '/rating/index?sort=' + sort + '&order=' + order + '&tab=players';
    });
});

// Сортировка для кланов
document.querySelectorAll('#tab-clans .sortable').forEach(el => {
    el.addEventListener('click', function() {
        const sort = this.getAttribute('data-sort');
        let order = this.getAttribute('data-order');
        
        window.location.href = '/rating/index?clan_sort=' + sort + '&clan_order=' + order + '&tab=clans';
    });
});

// Загружаем при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab === 'clans') {
        document.querySelector('.tab-btn[data-tab="clans"]').click();
    } else {
        loadPlayers();
        loadClans();
    }
});
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.rating-container {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.rating-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.rating-header h1 {
    color: #ffd700;
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

/* Вкладки */
.rating-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid #5c3a2a;
    padding-bottom: 10px;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 25px;
    font-size: 16px;
    font-weight: bold;
    color: #c9a87b;
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s;
}

.tab-btn:hover {
    color: #ffd700;
}

.tab-btn.active {
    color: #ffd700;
    background: #3d2317;
    border-bottom: 3px solid #ffd700;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Таблица */
.rating-table-container {
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
    overflow-x: auto;
}

.rating-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.rating-table th,
.rating-table td {
    padding: 12px 10px;
    text-align: center;
    border-bottom: 1px solid #5c3a2a;
}

.rating-table th {
    background: #2c1810;
    color: #ffd700;
    font-weight: bold;
    white-space: nowrap;
}

.rating-table td {
    color: #e6c8a0;
}

.rating-table tbody tr:hover {
    background: #4a2e1f;
}

/* Ширина столбцов */
.col-rank { width: 60px; }
.col-name { text-align: left; }
.col-level { width: 80px; }
.col-exp { width: 100px; }
.col-repa { width: 100px; }
.col-win { width: 80px; }
.col-kr { width: 100px; }
.col-clan-name { text-align: left; }
.col-members { width: 100px; }
.col-clan-exp { width: 120px; }
.col-clan-repa { width: 120px; }
.col-clan-win { width: 100px; }
.col-clan-master { width: 120px; }

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    color: #ffaa33;
}

.sort-icon {
    display: inline-block;
    margin-left: 5px;
    font-size: 10px;
}

.sort-icon.asc {
    transform: rotate(180deg);
}

.rank-number {
    font-weight: bold;
    color: #ffd700;
}

.player-name, .clan-name {
    color: #ffd700;
    font-weight: bold;
    text-align: left;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #c9a87b;
}

.pagination-container {
    padding: 15px;
    display: flex;
    justify-content: center;
    border-top: 1px solid #5c3a2a;
}

.pagination {
    display: flex;
    list-style: none;
    gap: 5px;
}

.pagination li a, .pagination li span {
    display: block;
    padding: 6px 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #c9a87b;
    text-decoration: none;
}

.pagination li a:hover {
    background: #8b5e3c;
    color: #ffd700;
}

.pagination .active span {
    background: #8b5e3c;
    color: #ffd700;
}

/* Адаптация */
@media (max-width: 900px) {
    .col-exp, .col-repa, .col-kr,
    .col-clan-exp, .col-clan-repa {
        display: none;
    }
    
    .rating-table th, .rating-table td {
        padding: 8px 5px;
        font-size: 12px;
    }
}

@media (max-width: 600px) {
    .col-level, .col-win, .col-members, .col-clan-win {
        display: none;
    }
    
    .col-name, .col-clan-name {
        min-width: 120px;
    }
}
</style>
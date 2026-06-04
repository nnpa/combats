<?php
use yii\widgets\LinkPager;
use yii\helpers\Html;

if (empty($players)): ?>
    <tr><td colspan="7" class="loading">Нет игроков</td></tr>
<?php else: ?>
    <?php $currentRank = $rank; ?>
    <?php foreach ($players as $player): ?>
        <tr>
            <td class="rank-number"><?= $currentRank++ ?></td>
            <td class="player-name"><?= Html::encode($player->username) ?></td>
            <td><?= $player->level ?></td>
            <td><?= number_format($player->exp, 0, '.', ' ') ?></td>
            <td><?= number_format($player->repa, 0, '.', ' ') ?></td>
            <td><?= $player->win ?></td>
            <td><?= number_format($player->kr, 0, '.', ' ') ?></td>
        </tr>
    <?php endforeach; ?>
    
    <?php if ($pagination->pageCount > 1): ?>
        <tr>
            <td colspan="7" class="pagination-container">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination'],
                    'nextPageLabel' => '→',
                    'prevPageLabel' => '←',
                ]) ?>
            </td>
        </tr>
    <?php endif; ?>
<?php endif; ?>
<?php
use yii\widgets\LinkPager;
use yii\helpers\Html;

if (empty($clans)): ?>
    <tr><td colspan="7" class="loading">Нет кланов</td></tr>
<?php else: ?>
    <?php $currentRank = $rank; ?>
    <?php foreach ($clans as $clan): ?>
        <tr>
            <td class="rank-number"><?= $currentRank++ ?></td>
            <td class="clan-name"><?= Html::encode($clan->name) ?></td>
            <td><?= $clan->getAttribute('members_count') ?: 0 ?></td>
            <td><?= number_format($clan->getAttribute('total_exp') ?: 0, 0, '.', ' ') ?></td>
            <td><?= number_format($clan->getAttribute('total_repa') ?: 0, 0, '.', ' ') ?></td>
            <td><?= number_format($clan->getAttribute('total_win') ?: 0, 0, '.', ' ') ?></td>
            <td><?= Html::encode($clan->admin->username ?? '—') ?></td>
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
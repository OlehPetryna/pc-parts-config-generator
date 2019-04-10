<?php
/**
 * @var PartsCollection $parts
 */

use App\Domain\PcParts\PartsCollection;
use App\Domain\PcParts\PcPart;

?>
<div id="summaryWrapper" class="px-3 py-1">
    <h2 class="mb-3">Перелік обраних деталей</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>Деталь</th>
                <th>Ціна</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($parts as $part): ?>
                <?php /**@var PcPart $part */ ?>
                <tr>
                    <td>
                        <div class="card mb-3">
                            <button class="card-header  text-left btn btn-link" data-toggle="collapse"
                                    data-target="#card-<?= $part->getKey() ?>">
                                <?= $part->title ?>
                            </button>
                            <div class="card-body collapse" id="card-<?= $part->getKey() ?>">
                                <div class="part-img d-block mx-auto mb-2">
                                    <img src="<?= $part->getLargeImg() ?? $part->img ?>"
                                         alt="<?= $part->title ?>"
                                         class="img-fluid">
                                </div>
                                <div class="table-responsive px-3 py-2">
                                    <table id="partsTable" class="table table-striped">
                                        <tbody>
                                        <?php foreach ($part->specifications as $specification): ?>
                                            <tr>
                                                <th><?= $specification['key'] ?></th>
                                                <td><?= $specification['value'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong><?= $part->price ?></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th>
                    <strong>Сума</strong>
                </th>
                <td>
                    <strong>
                        &dollar;
                        <?= round($parts->sum(function (PcPart $item) {
                            return (float)str_replace('$', '', $item->price);
                        }), 2) ?>
                    </strong>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>

<script>
    window.onload = function () {
        $('.part-img').each(function () {
            $(this).addClass('zoomable-image');
            $(this).zoom({
                url: $(this).find('img').attr('src')
            })
        })
    }
</script>
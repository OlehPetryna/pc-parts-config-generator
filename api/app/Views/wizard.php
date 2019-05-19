<?php
/**
 * @var int $currentStep
 * @var int $totalStepsAmount
 * @var string $stepName
 */
?>
<div class="whole-screen-min-height">
    <form action="" method="post">
        <input type="hidden" name="stage" id="currentStage" value="<?= $currentStep ?>">
        <input type="hidden" name="partId" id="pickedPartId">
        <div class="progress-wrapper mb-4">
            <div class="progress">
                <div class="progress-bar bg-primary" style="width: <?= $currentStep / $totalStepsAmount * 100 ?>%"></div>
            </div>
            <p class="px-3 mb-3"><?= "$currentStep / $totalStepsAmount" ?></p>
            <button type="button" class="ml-3 btn btn-secondary btn-sm btn-rewind-step">Назад</button>
            <a href="/wizard?refresh=refresh" class="ml-3 btn btn-secondary btn-sm">Почати спочатку</a>
        </div>
        <div class="container">
            <h3>Будь ласка, оберіть <?= $stepName ?></h3>
            <div class="table-responsive px-3 py-2">
                <table id="partsTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Зображення</th>
                        <th>Назва</th>
                        <th>Ціна</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
    window.onload = function () {
        const stage = $('#currentStage').val();
        const dataTable = $('#partsTable').DataTable({
            processing: true,
            paging: true,
            searchDelay: 500,
            columns: [
                {
                    data: null,
                    name: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                    render: function (currentEntry, type, current, settings) {
                        const idx = settings.row;
                        return `
                            <div class="d-flex flex-wrap">
                                <button type="button" data-id="${currentEntry._id}" class="w-100 mb-2 btn btn-outline-success choose-part-btn">Обрати</button>
                                <button type="button" data-idx="${idx}" class="w-100 part-details-btn btn btn-outline-info">Деталі</button>
                            </div>
                            `
                    }
                }, {
                    data: 'img',
                    name: 'img',
                    searchable: false,
                    orderable: false,
                    render: function (imgSrc, something, dataEntry) {
                        return `<img class="parts-table-image" src="${dataEntry.smallImg || imgSrc}" data-lg-img="${dataEntry.largeImg}"/>`
                    }
                }, {
                    data: 'title',
                    name: 'title',
                }, {
                    name: 'price',
                    data: 'priceNumber',
                    render: function (priceNumber, something, dataEntry) {
                        return dataEntry.price;
                    }
                },
            ],
            serverSide: true,
            ajax: `/fetch-stage-parts/?stage=${stage}`,
            language: {
                "decimal":        "",
                "emptyTable":     "Відповідних результатів не знайдено",
                "info":           "Відображається від _START_ до _END_ з _TOTAL_ результатів",
                "infoEmpty":      "Результатів немає",
                "infoFiltered":   "(відфільтровано з _MAX_ можливих результатів)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "Показати по _MENU_ записів",
                "loadingRecords": "Загрузка...",
                "processing":     "Обробка...",
                "search":         "Пошук:",
                "zeroRecords":    "Відповідних результатів не знайдено",
                "paginate": {
                    "first":      "Перша",
                    "last":       "Остання",
                    "next":       "Наступна",
                    "previous":   "Попередня"
                },
                "aria": {
                    "sortAscending":  ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            }
        });

        $(document).on('click', '.part-details-btn', function () {
            const data = dataTable.data()[$(this).data('idx')];
            let content = $(
                `<table data-id="${data._id}" class="table table-striped">
                    <tr><th>Ціна</th><td>${data.price}</td></tr>
                </table>`
            );

            for (let i in data.specifications) {
                const specification = data.specifications[i];
                content.append(`<tr><th>${specification.translation}</th><td>${specification.value}</td></tr>`);
            }

            swal({
                className: 'part-details-modal',
                title: data.title,
                content: content[0],
                buttons: {
                    success: {
                        text: 'Обрати',
                        value: true,
                        type: 'button',
                        className: 'choose-part-btn'
                    }
                }
            });

            $('.choose-part-btn').attr('type', 'button');

            setTimeout(function () {
                $('.swal-overlay--show-modal').scrollTop(0)
            }, 0)
        });

        $(document).on('click', '.choose-part-btn', function () {
            const partId = $(this).data('id') !== undefined
                ? $(this).data('id')
                : $(this).closest('.part-details-modal').find('[data-id]').data('id');

            const stage = $('#currentStage');
            stage.val(stage.val() * 1 + 1);

            console.log('setting od', partId);

            $('#pickedPartId').val(partId);
            $('form').submit();
        });

        $(document).on('click', '.btn-rewind-step', function () {
            window.location = '/rewind-wizard-step';
        });

        dataTable.on('draw.dt', function () {
            $('.parts-table-image').each(function () {
                const lgImg = $(this).data('lg-img');
                if (lgImg) {
                    $(this).addClass('zoomable-image');
                    $(this).closest('td').zoom({
                        url: lgImg,
                    })
                }
            });
        });
    };
</script>
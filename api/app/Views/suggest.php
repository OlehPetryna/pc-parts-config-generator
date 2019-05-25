<?php
/**
 * @var bool $showErrorMessage
 */
$questions = [
    'Question[gaming]' => 'Компютерні ігри',
    'Question[multimedia]' => 'Перегляд фільмів, серіалів; прослуховування музики',
    'Question[light-usage]' => 'Веб-браузинг, використання офісного пакету програм (Word, Excel, тощо)',
    'Question[graphics]' => 'Професіональне використання (діяльність повязана з графікою)',
    'Question[cpu-intensive]' => 'Професіональне використання (діяльність повязана з обчисленнями)',
];

$maxAvailablePriority = count($questions);
?>
<div id="suggestLoader" class="spinner-grow text-primary" role="status">
    <span class="sr-only">Loading...</span>
</div>
<div id="suggestPage" class="whole-screen-min-height">
    <div class="container">
        <?php if ($showErrorMessage): ?>
            <div class="alert alert-danger">
                <h3>Упс .... щось пійшло не так .... Ми не змогли Вам допомогти. </h3>
                <h5 class="text-secondary">Будь ласка, спробуйте ще раз, або змініть розставлені пріорітети\бюджет</h5>
            </div>
        <?php else: ?>
            <h3>
                Нам необхідно знати, як само Ви збираєтесь використовувати Ваш ПК
            </h3>
            <h4 class="text-secondary">
                Розставте пріоритети можливих сценаріїв використання
            </h4>
        <?php endif; ?>

        <form action="/complete-suggestion" method="post" id="suggestForm">
            <div id="questions-wrapper" class="w-75 mx-auto mt-4">
                <?php foreach ($questions as $name => $text): ?>
                    <div class="question row mx-0">
                    <span class="question-text col-12 col-sm-12 col-md-6">
                        <?= $text ?>
                    </span>
                        <span class="priorities col-12 col-sm-12 col-md-6">
                        <?php foreach (range(0, $maxAvailablePriority) as $priority): ?>
                            <input type="radio"
                                   name="<?= $name ?>"
                                   id="<?= $name . $priority ?>"
                                   class="priority-radio"
                                   value="<?= $priority ?>"
                            >
                            <label for="<?= $name . $priority ?>" class="priority-btn">
                                <?= $priority ?>
                            </label>
                        <?php endforeach; ?>
                    </span>
                    </div>
                <?php endforeach; ?>
                <div class="question row mx-0 form-group">
                    <label class="question-text">Бюджет (максимальна сума) : </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input name="budget" type="number" required class="form-control">
                    </div>
                </div>
                <div class="text-center w-100 mx-auto">
                    <button type="submit" class="btn btn-primary btn-lg mt-1 mx-auto">Підібрати</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    window.onload = function () {
        $('#suggestForm').on('submit', function (e) {
            const $page = $('#suggestPage');
            const $loader = $('#suggestLoader');
            const $form = $page.find('form');

            $page.fadeOut('fast', () => $loader.show());

            // $.ajax({
            //     url: $form.attr('action'),
            //     data: new FormData($form[0]),
            //     method: 'post',
            //     processData: false,
            //     contentType: false,
            //     success: ((data, status, request) => {})
            // });

            // return false;
        })
    };
</script>
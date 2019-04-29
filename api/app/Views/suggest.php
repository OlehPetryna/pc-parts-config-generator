<?php
$questions = [
    'Question[gaming]' => 'Компютерні ігри',
    'Question[multimedia]' => 'Перегляд фільмів, серіалів; прослуховування музики',
    'Question[light-usage]' => 'Веб-браузинг, використання офісного пакету програм (Word, Excel, тощо)',
    'Question[graphics]' => 'Професіональне використання (діяльність повязана з графікою)',
    'Question[cpu-intensive]' => 'Професіональне використання (діяльність повязана з обчисленнями)',
];

$maxAvailablePriority = count($questions);
?>
<div class="whole-screen-min-height py-2 px-3">
    <h2>
        Нам необхідно знати, як само Ви збираєтесь використовувати Ваш ПК
    </h2>
    <h4 class="text-secondary">
        Розставте пріоритети можливих сценаріїв використання
    </h4>
    <form action="/" method="post">
        <div id="questions-wrapper" class="w-75 mx-auto mt-4">
            <?php foreach ($questions as $name => $text): ?>
                <div class="question row mx-0">
                    <span class="question-text col-12 col-sm-12 col-md-7">
                        <?= $text ?>
                    </span>
                    <span class="priorities col-12 col-sm-12 col-md-5">
                        <?php foreach (range(0, $maxAvailablePriority) as $priority): ?>
                            <input type="radio"
                                   name="<?= $name ?>"
                                   id="<?= $name . $priority ?>"
                                   class="priority-radio"
                                   value="<?= $priority ?>">
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
                    <input type="number" class="form-control">
                </div>
            </div>
            <div class="text-center w-100 mx-auto">
                <button type="submit" class="btn btn-primary btn-lg mt-1 mx-auto">Підібрати</button>
            </div>
        </div>
    </form>
</div>
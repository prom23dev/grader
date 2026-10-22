<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'); ?>
<div class="container">
    <div class="wrapper">
        <div class="location">
            <?
            /** TODO перенести в result_modifier компонента вывода регионов */

            use \Bitrix\Main\Loader;
            use \Bitrix\Sale\Location;

            Loader::includeModule('sale');

            $regionRes = \Bitrix\Sale\Location\LocationTable::getList(array(
                'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID, 'TYPE_CODE' => 'REGION'),
                'select' => array('ID' => 'ID', 'NAME_RU' => 'NAME.NAME', 'TYPE_CODE' => 'TYPE.CODE'),
                'order' => array('NAME_RU' => 'ASC'),
            ));
            ?>
            <div class="region">
                <? while ($region = $regionRes->fetch()) : ?>
                    <?
                    $regionId = $region['ID'];
                    $typeCode = $region['TYPE_CODE'];
                    $regionNameRu = $region['NAME_RU'];
                    ?>

                    <h4 class="hash"><?= $regionNameRu; ?></h4>
                    <a href="#" class="oblast-get" data-id="<?= $regionId ?>" data-name="<?= $regionNameRu; ?>">Подробнее</a>

                <? endwhile; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.oblast-get', function () {
        let regionId = $(this).attr('data-id');
        let regionName = $(this).attr('data-name')
        $.post('../local/templates/grader/assets/ajax/getByRegion.php', {ID: regionId, NAME: regionName})
            .done(function (data) {
                $('.region').html('');
                let result = JSON.parse(data);
                result.forEach(item => {
                    $('.region').append(`
                            <h4 class="hash">${item.NAME}</h4>
                            <a href="#" class="oblast-get" data-id="${item.ID}" data-name="${item.NAME}">Подробнее</a>
                        `)
                });
            });
    });
</script>
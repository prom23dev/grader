<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
?>
<link rel="stylesheet" href="/local/templates/grader/components/bitrix/news.list/projects-list/style.css">
<link rel="stylesheet" href="/map/assets/css/style.css">

<div class="map-page">
    <div class="map-wrapper">
        <div class="grid-container">
            <div class="map-container-wrapper">
                <h3 class="map-container-title"><span class="map-step-number">1</span> Регион</h3>
                <div id="regions" class="map-container"></div>
            </div>
            <div class="map-container-wrapper">
                <h3 class="map-container-title"><span class="map-step-number">2</span> Район</h3>
                <div id="districts" class="map-container"></div>
            </div>
            <div class="map-container-wrapper">
                <h3 class="map-container-title"><span class="map-step-number">3</span> Населённый пункт</h3>
                <div id="settlements" class="map-container"></div>
            </div>
            <div id="projects" class="map-container"></div>
        </div>
    </div>
</div>
<script src="/map/assets/js/script.js"></script>
<script src="/map/assets/js/custom-settlement.js"></script>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
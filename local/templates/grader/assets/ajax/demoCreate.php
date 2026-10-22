<?php
ini_set('max_execution_time', 300);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('sale');
CModule::IncludeModule('iblock');

global $USER;

// ---------- Маппинг регионов на коды ----------
$regionCodes = [
    'Республика Адыгея' => '01',
    'Республика Башкортостан' => '02',
    'Республика Бурятия' => '03',
    'Республика Алтай' => '04',
    'Республика Дагестан' => '05',
    'Республика Ингушетия' => '06',
    'Кабардино-Балкарская Республика' => '07',
    'Кабардино-Балкарская республика' => '07',
    'Республика Калмыкия' => '08',
    'Карачаево-Черкесская Республика' => '09',
    'Карачаево-Черкесская республика' => '09',
    'Республика Карелия' => '10',
    'Республика Коми' => '11',
    'Республика Марий Эл' => '12',
    'Республика Мордовия' => '13',
    'Республика Саха (Якутия)' => '14',
    'Республика Северная Осетия — Алания' => '15',
    'Республика Северная Осетия - Алания' => '15',
    'Республика Татарстан' => '16',
    'Республика Тыва' => '17',
    'Удмуртская Республика' => '18',
    'Удмуртская республика' => '18',
    'Республика Хакасия' => '19',
    'Чеченская Республика' => '20',
    'Чеченская республика' => '20',
    'Чувашская Республика' => '21',
    'Чувашская республика' => '21',
    'Алтайский край' => '22',
    'Краснодарский край' => '23',
    'Красноярский край' => '24',
    'Приморский край' => '25',
    'Ставропольский край' => '26',
    'Хабаровский край' => '27',
    'Амурская область' => '28',
    'Архангельская область' => '29',
    'Астраханская область' => '30',
    'Белгородская область' => '31',
    'Брянская область' => '32',
    'Владимирская область' => '33',
    'Волгоградская область' => '34',
    'Вологодская область' => '35',
    'Воронежская область' => '36',
    'Ивановская область' => '37',
    'Иркутская область' => '38',
    'Калининградская область' => '39',
    'Калужская область' => '40',
    'Камчатский край' => '41',
    'Кемеровская область' => '42',
    'Кировская область' => '43',
    'Костромская область' => '44',
    'Курганская область' => '45',
    'Курская область' => '46',
    'Ленинградская область' => '47',
    'Липецкая область' => '48',
    'Магаданская область' => '49',
    'Московская область' => '50',
    'Мурманская область' => '51',
    'Нижегородская область' => '52',
    'Новгородская область' => '53',
    'Новосибирская область' => '54',
    'Омская область' => '55',
    'Оренбургская область' => '56',
    'Орловская область' => '57',
    'Пензенская область' => '58',
    'Пермский край' => '59',
    'Псковская область' => '60',
    'Ростовская область' => '61',
    'Рязанская область' => '62',
    'Самарская область' => '63',
    'Саратовская область' => '64',
    'Сахалинская область' => '65',
    'Свердловская область' => '66',
    'Смоленская область' => '67',
    'Тамбовская область' => '68',
    'Тверская область' => '69',
    'Томская область' => '70',
    'Тульская область' => '71',
    'Тюменская область' => '72',
    'Ульяновская область' => '73',
    'Челябинская область' => '74',
    'Забайкальский край' => '75',
    'Ярославская область' => '76',
    'Москва' => '77',
    'Санкт-Петербург' => '78',
    'Еврейская автономная область' => '79',
    'Ненецкий автономный округ' => '83',
    'Ханты-Мансийский автономный округ' => '86',
    'Ханты-Мансийский автономный округ - Югра' => '86',
    'Чукотский автономный округ' => '87',
    'Ямало-Ненецкий автономный округ' => '89',
    'Республика Крым' => '91',
    'Севастополь' => '92',
    'Байконур' => '94',
];

// ---------- JSON с геоданными ----------
$jsonPath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/grader/assets/json/geoDataWithCodes.json';
$json = file_get_contents($jsonPath);
$data = json_decode($json, true);

if (!$data || empty($data['region'])) {
    die('bad json');
}

// ---------- директория с картинками ----------
$imgDir = __DIR__ . '/region-img';

function uploadRegionImage($imgDir, $regionCode) {
    $formats = ['png', 'jpg', 'jpeg', 'svg'];

    foreach ($formats as $ext) {
        $sourcePath = $imgDir . '/' . $regionCode . '.' . $ext;

        if (file_exists($sourcePath)) {
            if ($ext === 'svg') {
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/regions/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destPath = $uploadDir . $regionCode . '.svg';

                if (!file_exists($destPath)) {
                    copy($sourcePath, $destPath);
                }

                $fileArray = [
                    'name'      => $regionCode . '.svg',
                    'size'      => filesize($destPath),
                    'tmp_name'  => $destPath,
                    'type'      => 'image/svg+xml',
                    'error'     => 0,
                    'MODULE_ID' => 'iblock'
                ];

                return CFile::SaveFile($fileArray, 'iblock');
            } else {
                $fileArray = CFile::MakeFileArray($sourcePath);
                return CFile::SaveFile($fileArray, 'iblock');
            }
        }
    }

    return false;
}

// ---------- файлы прогресса ----------
$posFile   = __DIR__ . '/project_import_pos.txt';
$totalFile = __DIR__ . '/project_import_total.txt';

$startIndex = 0;
if (file_exists($posFile)) {
    $startIndex = (int)file_get_contents($posFile);
}

$totalCreated = 0;
if (file_exists($totalFile)) {
    $totalCreated = (int)file_get_contents($totalFile);
}

// ---------- значения по умолчанию ----------
$dopInfo = '';
$name    = 'Автоимпорт';
$phone   = '';
$email   = '';
$userId  = $USER->IsAuthorized() ? $USER->GetID() : 1;

$el = new CIBlockElement;

$limitPerRun = 1000;
$created     = 0;
$globalIndex = 0;

$createdRegions  = [];
$createdDistrict = [];

echo "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>";
echo "<h2>🚀 Импорт проектов</h2>";
echo "<p>📊 Начинаем с позиции: <strong>$startIndex</strong></p>";
echo "<p>✅ Всего создано ранее: <strong>$totalCreated</strong></p>";
echo "<p>⏳ Создаем до <strong>" . ($limitPerRun) . "</strong> элементов за этот шаг...</p>";
echo "<hr>";
flush();

foreach ($data['region'] as $regionName => $districts) {

    $regionCode = isset($regionCodes[$regionName]) ? $regionCodes[$regionName] : null;

    if (!$regionCode) {
        file_put_contents(
            __DIR__ . '/project_import_errors.log',
            date('c') . " | WARNING: No code found for region=$regionName" . PHP_EOL,
            FILE_APPEND
        );
        continue;
    }

    $photoId       = uploadRegionImage($imgDir, $regionCode);
    $districtIndex = 0;

    // ---------- 1) Проект уровня РЕГИОНА ----------
    if (!in_array($regionName, $createdRegions, true)) {

        $addressRegion  = $regionName;
        $projectNameReg = "Общий проект. " . $regionName;
        $codeReg        = $regionCode . '-' . CUtil::translit($regionName, "ru");

        // Проверяем, есть ли уже такой региональный проект по CODE
        $existsReg = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, '=CODE' => $codeReg],
            false,
            false,
            ['ID']
        )->Fetch();

        if ($existsReg) {
            echo "⚠️ Регион уже существует, пропуск: $projectNameReg<br>";
        } else {
            $PROP_REG = [
                "PR_LOCATION"        => $regionName,
                "PR_ADDRESS"         => $addressRegion,
                "PR_REGION"          => $regionCode,
                "PR_STATUS"          => 7,
                "PR_ADDITIONAL_INFO" => $dopInfo,
                "PR_USER_NAME"       => $name,
                "PR_USER_PHONE"      => $phone,
                "PR_EMAIL"           => $email,
                "PR_USER_DATA"       => $userId,
                "PR_COST"            => 0,
            ];

            if ($photoId) {
                $PROP_REG["PR_PHOTO"] = $photoId;
            }

            $arLoadProductArrayReg = [
                "MODIFIED_BY"       => $userId,
                "IBLOCK_SECTION_ID" => 1,
                "IBLOCK_ID"         => PROJECTS_IBLOCK_ID,
                "PROPERTY_VALUES"   => $PROP_REG,
                "NAME"              => $projectNameReg,
                "CODE"              => $codeReg,
                "ACTIVE"            => "Y",
            ];

            $PRODUCT_ID = $el->Add($arLoadProductArrayReg);
            if (!$PRODUCT_ID) {
                file_put_contents(
                    __DIR__ . '/project_import_errors.log',
                    date('c') . " | type=region | region=$regionName | ERROR: " . $el->LAST_ERROR . PHP_EOL,
                    FILE_APPEND
                );
                echo "❌ Ошибка региона (см. лог): $projectNameReg<br>";
            } else {
                echo "✓ Регион создан: $projectNameReg (ID=$PRODUCT_ID)<br>";
                $created++;
            }
        }

        $createdRegions[] = $regionName;
    }

    // ---------- 2) Районы и НП ----------
    foreach ($districts as $districtItem) {

        $districtName = $districtItem['district'];
        $settlements  = $districtItem['settlement'];

        $districtIndex++;

        // ---------- РАЙОН ----------
        $districtKey = $regionName . '|' . $districtName;
        if (!in_array($districtKey, $createdDistrict, true)) {

            if ($globalIndex >= $startIndex) {

                $addressDistrict = $regionName . ', ' . $districtName;
                $districtCode = $regionCode . '-' . $districtIndex;
                $projectNameDist = "Общий проект. " . $districtName;
                $codeDist        = $districtCode . '-' . CUtil::translit($districtName, "ru");

                // Проверяем, есть ли уже такой районный проект по CODE
                $existsDist = CIBlockElement::GetList(
                    [],
                    ['IBLOCK_ID' => PROJECTS_IBLOCK_ID, '=CODE' => $codeDist],
                    false,
                    false,
                    ['ID']
                )->Fetch();

                if ($existsDist) {
                    echo "⚠️ Район уже существует, пропуск: $projectNameDist<br>";
                } else {
                    $PROP_DIST = [
                        "PR_LOCATION"          => $regionName,
                        "PR_LOCATION_DISTRICT" => $districtName,
                        "PR_ADDRESS"           => $addressDistrict,
                        "PR_REGION"            => $districtCode,
                        "PR_STATUS"            => 7,
                        "PR_ADDITIONAL_INFO"   => $dopInfo,
                        "PR_USER_NAME"         => $name,
                        "PR_USER_PHONE"        => $phone,
                        "PR_EMAIL"             => $email,
                        "PR_USER_DATA"         => $userId,
                        "PR_COST"              => 0,
                    ];

                    if ($photoId) {
                        $PROP_DIST["PR_PHOTO"] = $photoId;
                    }

                    $arLoadProductArrayDist = [
                        "MODIFIED_BY"       => $userId,
                        "IBLOCK_SECTION_ID" => 1,
                        "IBLOCK_ID"         => PROJECTS_IBLOCK_ID,
                        "PROPERTY_VALUES"   => $PROP_DIST,
                        "NAME"              => $projectNameDist,
                        "CODE"              => $codeDist,
                        "ACTIVE"            => "Y",
                    ];

                    $PRODUCT_ID = $el->Add($arLoadProductArrayDist);
                    if (!$PRODUCT_ID) {
                        file_put_contents(
                            __DIR__ . '/project_import_errors.log',
                            date('c') . " | type=district | region=$regionName | district=$districtName | ERROR: " . $el->LAST_ERROR . PHP_EOL,
                            FILE_APPEND
                        );
                        echo "❌ Ошибка района (см. лог): $projectNameDist<br>";
                    } else {
                        echo "✓ Район создан: $projectNameDist (ID=$PRODUCT_ID)<br>";
                        $created++;
                    }
                }

                if ($created >= $limitPerRun) {
                    $totalCreated += $created;
                    file_put_contents($posFile, $globalIndex + 1);
                    file_put_contents($totalFile, $totalCreated);
                    echo "<hr><h3>✅ Шаг завершен!</h3>";
                    echo "<p>Создано в этом шаге: <strong>$created</strong></p>";
                    echo "<p>Всего создано: <strong>$totalCreated</strong></p>";
                    echo '<meta http-equiv="refresh" content="2">';
                    echo "</body></html>";
                    die();
                }
            }

            $createdDistrict[] = $districtKey;
            $globalIndex++;
        }

        // ---------- 3) НП ----------
        $settlementIndex = 0;

        foreach ($settlements as $settlementName) {

            $settlementIndex++;

            if ($globalIndex < $startIndex) {
                $globalIndex++;
                continue;
            }

            $address     = $regionName . ', ' . $districtName . ', ' . $settlementName;
            $settlementCode = $regionCode . '-' . $districtIndex . '-' . $settlementIndex;
            $projectName = "Общий проект. " . $settlementName;
            $codeSettl = $settlementCode . '-' . CUtil::translit($settlementName, "ru");

            // Проверяем, есть ли уже такой НП-проект по CODE
            $existsFilter = [
                'IBLOCK_ID' => PROJECTS_IBLOCK_ID,
                '=NAME' => $projectName,
                '=PROPERTY_PR_REGION' => $settlementCode  // Уникальный код региона
            ];
            $existsSettl = CIBlockElement::GetList([], $existsFilter, false, false, ['ID'])->Fetch();

            if ($existsSettl) {
                echo "⚠️ НП уже существует, пропуск: $projectName<br>";
            } else {

                $PROP = [
                    "PR_LOCATION"            => $regionName,
                    "PR_LOCATION_DISTRICT"   => $districtName,
                    "PR_LOCATION_SETTLEMENT" => $settlementName,
                    "PR_REGION"              => $settlementCode,
                    "PR_ADDRESS"             => $address,
                    "PR_STATUS"              => 7,
                    "PR_ADDITIONAL_INFO"     => $dopInfo,
                    "PR_USER_NAME"           => $name,
                    "PR_USER_PHONE"          => $phone,
                    "PR_EMAIL"               => $email,
                    "PR_USER_DATA"           => $userId,
                    "PR_COST"                => 0,
                ];

                if ($photoId) {
                    $PROP["PR_PHOTO"] = $photoId;
                }

                $arLoadProductArray = [
                    "MODIFIED_BY"       => $userId,
                    "IBLOCK_SECTION_ID" => 1,
                    "IBLOCK_ID"         => PROJECTS_IBLOCK_ID,
                    "PROPERTY_VALUES"   => $PROP,
                    "NAME"              => $projectName,
                    "CODE"              => $codeSettl,
                    "ACTIVE"            => "Y",
                ];

                $PRODUCT_ID = $el->Add($arLoadProductArray);
                if (!$PRODUCT_ID) {
                    file_put_contents(
                        __DIR__ . '/project_import_errors.log',
                        date('c') . " | type=settlement | region=$regionName | district=$districtName | settlement=$settlementName | ERROR: " . $el->LAST_ERROR . PHP_EOL,
                        FILE_APPEND
                    );
                    echo "❌ Ошибка НП (см. лог): $projectName<br>";
                } else {
                    echo "✓ НП создан: $projectName (ID=$PRODUCT_ID)<br>";
                    $created++;
                }
            }

            $globalIndex++;

            if ($created >= $limitPerRun) {
                $totalCreated += $created;
                file_put_contents($posFile, $globalIndex);
                file_put_contents($totalFile, $totalCreated);
                echo "<hr><h3>✅ Шаг завершен!</h3>";
                echo "<p>Создано в этом шаге: <strong>$created</strong></p>";
                echo "<p>Всего создано: <strong>$totalCreated</strong></p>";
                echo '<meta http-equiv="refresh" content="2">';
                echo "</body></html>";
                die();
            }
        }
    }

    $processedRegions++;
}

if (file_exists($posFile)) {
    unlink($posFile);
}
if (file_exists($totalFile)) {
    unlink($totalFile);
}

echo '<br><br>✅ ALL_DONE: Processed ' . $processedRegions . ' regions, Created ' . $created . ' items';
echo "</body></html>";

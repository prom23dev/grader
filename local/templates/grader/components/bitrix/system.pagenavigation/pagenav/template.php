<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$countOfPages = $arResult['NavPageNomer'];
?>
<? if ($arResult["NavPageCount"] > 1): ?>
    <div class="projects-page__pagination">
        <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" id="prev">
            <button class="projects-page__pagination--prev-page ">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M16 4V4C22.628 4 28 9.372 28 16V16C28 22.628 22.628 28 16 28V28C9.372 28 4 22.628 4 16V16C4 9.372 9.372 4 16 4Z"
                          stroke="#BDBDBD" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.6668 15.9998L21.3335 15.9998" stroke="#BDBDBD" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <path d="M14.6665 20L10.6665 16L14.6665 12" stroke="#BDBDBD" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>
        </a>
        <p class=""><span class="page-num"><?=$arResult['NavPageNomer']?></span><?=' - '.$arResult['NavPageCount']?></p>
        <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" id="next">
            <button class="projects-page__pagination--next-page">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M16 28V28C9.372 28 4 22.628 4 16V16C4 9.372 9.372 4 16 4V4C22.628 4 28 9.372 28 16V16C28 22.628 22.628 28 16 28Z"
                          stroke="#333333" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21.3332 16.0002H10.6665" stroke="#333333" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <path d="M17.3335 12L21.3335 16L17.3335 20" stroke="#333333" stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>
        </a>
    </div>
<? endif;?>
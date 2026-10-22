<?php
if (rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/') === '/projects/create.php') {
    header('Location: /projects/create/', true, 301);
    exit;
}
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'); ?>

<?
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJS(SITE_DIR."projects/assets/js/script.js");
Asset::getInstance()->addCss(SITE_DIR."projects/assets/css/style.css");
global $USER;
$rsUser = CUser::GetByID($USER->GetParam('USER_ID'));
$arUser = $rsUser->Fetch();
global $myphoneNumber;
$myphoneNumber=$arUser["PERSONAL_PHONE"];
if ($USER->IsAuthorized()):
?>
<div class="wrapper">
    <div id="create-project-container" class="container">
    <h1>Создать проект</h1>
        <form class="content" id="create-project-form">


            <div class="create-project__item" id="region">
                <label  for="region"><span class="required-sign">*</span> Выберите область</label>
                <select id="region-select">
                    <option value="" selected>Регион, Край, область</option>
                </select>
            </div>
            <div class="create-project__item" id="district">
                <label  for="region"> Выберите район</label>
                <select id="district-select">
                    <option value="" selected>Район</option>
                </select>
            </div>
            <div class="create-project__item" id="settlement">
                <label  for="region"> Выберите населенный пункт</label>
                <select id="settlement-select">
                    <option value="" selected>Населенный пункт</option>
                </select>
            </div>

            <div class="create-project__item" id="street-container">
                <label for="street"><span class="required-sign">*</span> Укажите адрес</label>
                <input required type="text" name="street" id="street" placeholder="Улица, дом"/>
            </div>

            <div class="create-project__item" id="type-of-work">
                <label for="work-type"><span class="required-sign">*</span> Выберите вид работ</label>
                <select required name="work-type" id="work-type">
                    <optgroup label="Виды работ">
                        <option value="1">Асфальтирование</option>
                        <option value="2">Грейдирование</option>
                        <option value="3">Грейдирование с добавлением нового материала</option>
                        <option value="4">Поставка инертного материала (щебень, отсев, песок, ГПС)</option>
                        <option value="5">Благоустройство/содержание (озеленение)</option>
                    </optgroup>
                </select>
                <div class="wrapper" id="wrp">
                    <a href="#" id="type" class="font-weight-300" data-target="#mdl_Item" data-toggle="modal">Предложить свой вариант</a>
                </div>
            </div>

            <div class="create-project__item" id="additional-information">
                <label for="dop-info">Дополнительная информация</label>
                <textarea name="dop-info" id="dop-info" placeholder="Дополнительная информация по виду работ"></textarea>
            </div>
            <div class="create-project__item">
                <h4>Добавьте фотографии</h4>
                <p class="create-project__add-photo-annotation">Можно прикрепить до 5 фотографий</p>
                <div class="img-container">
                    <div>
                        <label for="file">
                            <img src="<?= SITE_TEMPLATE_PATH . '/assets/img/projects/add_photo.png' ?>" alt="Добавить фото">
                        </label>
                    </div>

                    <input type="file" multiple name="files[]" id="file" hidden/>

                    <div class="delete-button-container">
                        <input type="button" value="X"/>
                    </div>

                    </div>
            </div>

            <div class="create-project__item">
                <label for="name">
                    <span class="required-sign">*</span> Ваше имя</label>
                <input required type="text" name="name" id="name" placeholder="Имя пользователя" value="<?=$USER->GetParam("FIRST_NAME")?>"/>
            </div>
            <div class="create-project__item">
                <label for="phone">
                    <span class="required-sign">*</span> Телефон</label>
                <input required type="tel" name="phone"  id="phone"  placeholder="+ 7 (ХХХ) ХХХ ХХ ХХ" value="<?=$myphoneNumber?>"/>
            </div>
            <div class="create-project__item">
                <label for="email">
                    <span class="required-sign">*</span> Почта</label>
                <input required
                       type="email" pattern="[A-Za-z0-9_.\-]+@[A-Za-z0-9_.\-]+\.[A-Za-z]{2,8}"
                       name="email"
                       id="email"
                       placeholder="abc_./@email.ru" value="<?=$USER->GetParam("EMAIL")?>"/>
            </div>
            <p class="create-project__required-field-annotation"><span class="required-sign">*</span> поля обязательные к заполнению</p>
            <?php
            $consentIdPrefix = 'create-project';
            include $_SERVER['DOCUMENT_ROOT'] . '/include/consent-checkboxes.php';
            ?>
            <div class="create-project__create-button-block">
                <button type="submit" name="submit" id="submit" class="create-project__create-button">Создать проект</button>
            </div>
        </form>


        <dialog class="dialog" id="success-dialog">
            <form method="dialog">
                <div class="closeModal">
                    <button>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.1213 11.8495L20.4852 5.48558L18.3639 3.36426L11.9999 9.72822L5.63598 3.36426L3.51466 5.48558L9.87862 11.8495L3.51465 18.2135L5.63597 20.3348L11.9999 13.9709L18.3639 20.3348L20.4852 18.2135L14.1213 11.8495Z" fill="#474A51"/>
                        </svg>
                    </button>
                </div>
                <div class="success-dialog__content">
                    <h1>Поздравляем!</h1>
                    <div>
                        <p>Проект успешно создан и отправлен на модерацию.</p>
                        <p>Отслеживать статус проекта вы можете в <a href="#">Личном кабинете</a> в разделе «Мои проекты».</p>
                    </div>
                </div>
            </form>
        </dialog>


        <dialog class="dialog" id="mdl_Item">
            <form method="dialog">
                <div class="closeModal">
                    <button>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.1213 11.8495L20.4852 5.48558L18.3639 3.36426L11.9999 9.72822L5.63598 3.36426L3.51466 5.48558L9.87862 11.8495L3.51465 18.2135L5.63597 20.3348L11.9999 13.9709L18.3639 20.3348L20.4852 18.2135L14.1213 11.8495Z" fill="#474A51"/>
                        </svg>
                    </button>
                </div>
                <div class="custom-work-type__content">
                    <h1>Предложите свой вариант</h1>
                    <div class="create-project__item">
                        <label for="custom-work-type">Укажите вид работ</label>
                        <input type="text" id="custom-work-type" placeholder="Введите вид работ">
                    </div>
                    <button type="button" id="custom-work-type-save" class="create-project__create-button">Добавить</button>
                </div>
            </form>
        </dialog>

        <dialog class="dialog" id="failed-dialog">
            <form method="dialog">
                <div class="closeModal">
                    <button>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.1213 11.8495L20.4852 5.48558L18.3639 3.36426L11.9999 9.72822L5.63598 3.36426L3.51466 5.48558L9.87862 11.8495L3.51465 18.2135L5.63597 20.3348L11.9999 13.9709L18.3639 20.3348L20.4852 18.2135L14.1213 11.8495Z" fill="#474A51"/>
                        </svg>
                    </button>
                </div>
                <div class="failed-dialog__content">
                    <h1>Что-то пошло не так при создании проекта.</h1>
                    <div class="failed-dialog__message">
                        <p>Попробуйте ещё раз.</p>
                        <button>
                            Закрыть
                        </button>
                    </div>

                </div>
            </form>
        </dialog>
    </div>
</div>
<?else:?>
<? header("Location:/");?>
<?endif;?>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>

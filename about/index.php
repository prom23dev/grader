<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle("О нас");

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCSS("/about/style.css");
Asset::getInstance()->addJS("/about/script.js");
Asset::getInstance()->addJS("https://code.jquery.com/ui/1.14.1/jquery-ui.js");
?>
    <div class="container">
        <section class="about">
            <div class="about__main--content">
                <h1>О нас</h1>
                <p class="about__support-text">Если вам понравилась идея сервиса, вы можете <a href="/projects/support.php">поддержать</a>
                    его развитие.</p>
                <div class="about__main--information">
                    <h2>Главное</h2>
                    <p>«Жёлтый грейдер» – проект, разработанный с целью улучшения качества жизни горожан через активное
                        участие неравнодущных граждан в жизни своих населённых пунктов. На портале любой может сообщить
                        о проблемах в содержании объектов дорожной инфраструктуры (наличии ям, неисправности покрытия и
                        пр.) путём создания проекта, найти единомышленников, которые могут оказать финансовую помощь и,
                        собрав необходимую сумму, начнём реализовывать ваш проект. Вы ставите задачу — мы её решаем. А
                        средства находим вместе.</p>
                </div>
                <div class="FAQ" id="faq">
                    <h2>Часто задаваемые вопросы</h2>
                    <div id="FAQ__accordion">
                        <div class="FAQ__accordion-item">
                            <h4>Что делает этот проект «Желтый грейдер»?</h4>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.1213 11.8495L20.4852 5.48558L18.3639 3.36426L11.9999 9.72822L5.63598 3.36426L3.51466 5.48558L9.87862 11.8495L3.51465 18.2135L5.63597 20.3348L11.9999 13.9709L18.3639 20.3348L20.4852 18.2135L14.1213 11.8495Z"
                                      fill="#474A51"/>
                            </svg>
                        </div>
                        <div>
                            <div class="FAQ__accordion-content">
                                <p>Проект «Желтый грейдер» направлен на улучшение качества жизни горожан, предоставляя возможностей для участия в собрании, обсуждения и решения местных проблем в строительстве и дорожной инфраструктуре.</p>
                            </div>
                        </div>

                        <div class="FAQ__accordion-item">
                            <h4>В каких регионах или населенных пунктов предлагается организовать проект?</h4>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M5 12H19" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="FAQ__accordion-content">
                                <p>Проект предполагает создание нескольких участников в регионах, где заинтересованные и проблематичные области для развития.</p>
                            </div>
                        </div>
                        <div class="FAQ__accordion-item">
                            <h4>Какие проблемы планируются решить?</h4>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M5 12H19" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="FAQ__accordion-content">
                                <p>Проект планирует решить вопросы по улучшению дорожногоINFраструктурного условия и созданию общественных возможностей.</p>
                            </div>
                        </div>
                        <div class="FAQ__accordion-item">
                            <h4>Как будет организована деятельность и реализация проекта?</h4>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5V19" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M5 12H19" stroke="#474A51" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="FAQ__accordion-content">
                                <p>Проект будет организован в соответствии с законодательством и согласованиями местного исполнительного органа, включая проведение собраний и формирование комиссии для принятия решений.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="about__documents">
                    <h2>Документы</h2>
                    <div class="about__links">
                        <a href="/output/pdf/personal-data-consent.pdf" target="_blank" rel="noopener">Согласие на обработку персональных данных</a>
                        <a href="/output/pdf/privacy-policy.pdf" target="_blank" rel="noopener">Политика конфиденциальности</a>
                        <a href="/output/pdf/public-offer.pdf" target="_blank" rel="noopener">Оферта</a>
                    </div>

                </div>
                <div class="about__requisites" id="requisites">
                    <h2>Контакты и реквизиты</h2>
                    <p>Общество с ограниченной ответственностью «Жёлтый Грейдер»</p>
                    <dl class="about__requisites-list">
                        <div><dt>ОГРН</dt><dd>1252300040013</dd></div>
                        <div><dt>ИНН</dt><dd>2312336923</dd></div>
                        <div><dt>Адрес</dt><dd>350080, Краснодарский край, г. Краснодар, ул. Сормовская, д. 179/1</dd></div>
                        <div><dt>Электронная почта</dt><dd><a href="mailto:zhgrader@yandex.ru">zhgrader@yandex.ru</a></dd></div>
                        <div><dt>Телефон</dt><dd><a href="tel:+79094568723">+7 909 456-87-23</a></dd></div>
                    </dl>
                </div>
                <div class="about__about-you">
                    <h2>Не о нас, а о вас</h2>
                    <p>Этот блок пока находится в стадии разработки</p>
                </div>
            </div>
        </section>
    </div>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>

$(document).ready(function(){
    function pagerLinkUpdater() {
        const pageInfo = document.querySelector('.page-num');
        if (!pageInfo || !pageInfo.parentElement) {
            return;
        }

        const currentPage = parseInt(pageInfo.textContent.trim(), 10); // Текущая страница
        const totalPages = parseInt(pageInfo.parentElement.textContent.split('-')[1].trim(), 10);

        const prevButton = document.querySelector('.projects-page__pagination--prev-page');
        const nextButton = document.querySelector('.projects-page__pagination--next-page');

        if (!prevButton || !nextButton) {
            return;
        }

        const prevSvgPaths = prevButton.querySelectorAll('.projects-page__pagination--prev-page svg path');
        const nextSvgPaths = nextButton.querySelectorAll(' .projects-page__pagination--next-page svg path');

        if (currentPage === 1) {
            prevSvgPaths.forEach(path =>path.style.stroke = '#BDBDBD');
            prevButton.style.cursor = 'not-allowed';
            prevButton.disabled = true;
        }
        if (currentPage < totalPages && currentPage !== 1) {
            prevSvgPaths.forEach(path =>path.style.stroke = '#333333');
        }
        if (currentPage === totalPages) {
            prevSvgPaths.forEach(path =>path.style.stroke = '#333333');
            nextSvgPaths.forEach(path =>path.style.stroke = '#BDBDBD');
            nextButton.style.cursor = 'not-allowed';
            nextButton.disabled = true;
        }
    }
    pagerLinkUpdater();

        // Функционал пагинации без перезагрузки
        $(document).on('click', '.projects-page__pagination a', function (e) {
            let blockElements = $('.projects-page__pagination').parent().prev().attr('class');
            let href = $(this).attr('href');
            if (!href) {
                return true;
            }
            e.preventDefault();
            $.ajax({
                method: "POST",
                url: href,
                success: function (response) {
                    let html = "<div>" + response + "</div>";
                    let elements = $(html).find('.'+blockElements).html();
                    $('.'+blockElements).html(elements);
                    let pagination = $(html).find('#pag').html();
                    $('#pag').html(pagination);
                pagerLinkUpdater();

                }
            });
        })
})

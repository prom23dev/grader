$(document).ready(function () {
    $('.reviews__mobile-show-more').on('click', function (e) {
        e.preventDefault();

        const $button = $(this)
        const $content = $(this).siblings('.reviews__item--content');
        const isCollapsed = $button.attr('data-state') === 'collapsed';

        if (isCollapsed) {
            $content.css('opacity', 0);
            setTimeout(() => {
                $content.addClass('expanded')
                $content.css('opacity', 1);
            }, 150);

            $button.attr('data-state', 'expanded')
            $button.text('Свернуть')
        } else {
            $content.css('opacity', 0);
            setTimeout(() => {
                $content.removeClass('expanded')
                $content.css('opacity', 1);
            }, 150);

            $button.attr('data-state', 'collapsed')
            $button.text('Читать полностью')
        }
    })
    // Гениальный код Никиты по сохранению отзыва
    const reviewText = $('#review-text');

    reviewText.val(localStorage.getItem('review')?localStorage.getItem('review'):'')
})
$(document).ready(function() {
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps  : '#sitemaps',
        index_type: 'index.html'
    });
});

$('.reviews__mobile-show-more').on('click', function (e) {
    e.preventDefault();

    const $button = $(this)
    const $content = $(this).siblings('.reviews__item--content');
    const isCollapsed= $button.attr('data-state') === 'collapsed';

    if(isCollapsed){
        $content.css('opacity', 0);
        setTimeout(() => {
            $content.addClass('expanded')
            $content.css('opacity', 1);
        }, 150);

        $button.attr('data-state', 'expanded')
        $button.text('Свернуть')
    }
    else{
        $content.css('opacity', 0);
        setTimeout(() => {
            $content.removeClass('expanded')
            $content.css('opacity', 1);
        }, 150);

        $button.attr('data-state', 'collapsed')
        $button.text('Читать полностью')
    }
})

const successDialog = document.getElementById('success-send-review-dialog');
const failedDialog = document.getElementById('failed-send-review-dialog')
const openSuccessDialog = (e) => {
    e.preventDefault();
    const form = document.querySelector('.reviews__send-review-form');
    if (form.checkValidity()) {
        openDialog(successDialog);
    } else {
        form.reportValidity();
    }
}

const button = document.querySelector('.reviews_send-review--button');
button.addEventListener('click', openSuccessDialog);

$(document).ready(function() {
    $("#FAQ__accordion").accordion({
        collapsible: true,
        icons: false,
        heightStyle: "content",
        animate: 500
    });

    $('.FAQ__accordion-item').on('click', function ()
    {
        $('.FAQ__accordion-item').each(function () {
            $(this).find('svg').replaceWith(`
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 12H19" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `);
        });
        if ($(this).hasClass('ui-state-active')) {
            $(this).find('svg').replaceWith(`
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.1213 11.8495L20.4852 5.48558L18.3639 3.36426L11.9999 9.72822L5.63598 3.36426L3.51466 5.48558L9.87862 11.8495L3.51465 18.2135L5.63597 20.3348L11.9999 13.9709L18.3639 20.3348L20.4852 18.2135L14.1213 11.8495Z" fill="#474A51"/>
            </svg>
        `);
        }
    });
});


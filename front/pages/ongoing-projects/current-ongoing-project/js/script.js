$(document).ready(function() {
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps  : '#sitemaps',
        index_type: 'index.html'
    });
    const grid = document.querySelector('.current-project__images');
    const images = grid.querySelectorAll('img');

    if (images.length === 2) {
        grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
    } else{
        grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
    }

    function formatNumberWithSpaces(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    const investorAmounts = document.querySelectorAll('.current-project__investors-item h2:last-child');

    investorAmounts.forEach(function(element) {
        const text = element.textContent;
        const numberMatch = text.match(/(\d+(?:\s*\d+)*)/);
        if (numberMatch) {
            const number = numberMatch[1].replace(/\s/g, '');
            const formattedNumber = formatNumberWithSpaces(parseInt(number));
            element.textContent = text.replace(numberMatch[1], formattedNumber);
        }
    });
});
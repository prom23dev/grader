$(document).ready(function() {
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps  : '#sitemaps',
        index_type: 'index.html'
    });
});
const pageNext = () => {
    let page = document.querySelector('.page-num');
    let currentPage = Number(page.textContent);
    currentPage++;
    page.textContent = currentPage.toString();
    if (currentPage > 1) {
        document.querySelector('.projects__pagination--prev-page svg').classList.add('active');
    }
    console.log(currentPage)
}
const pagePrev = () => {
    let page = document.querySelector('.page-num')
    let currentPage = Number(page.textContent);
    if (currentPage > 1){
        currentPage--;
        page.textContent = currentPage.toString();
    }
    if (currentPage === 1) {
        document.querySelector('.projects__pagination--prev-page svg').classList.remove('active');
    }
    console.log(currentPage)
}
const nextPage = document.querySelector('.projects__pagination--next-page').onclick = pageNext;
const prevPage = document.querySelector('.projects__pagination--prev-page').onclick = pagePrev;



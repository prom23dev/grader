$(document).ready(function() {
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps: '#sitemaps',
        index_type: 'index.html'
    });
});
function redirectToCreateProject(){
    window.location.href='/projects/create/'
}

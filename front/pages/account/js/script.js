$(document).ready(function() {
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps  : '#sitemaps',
        index_type: 'index.html'
    });

});
const deleteDialog = document.getElementById('deleteDialog')
const leaveDialog = document.getElementById('leaveDialog');
function closeDialog(dialog) {
    dialog.close();
    dialog.classList.remove('show');
}
function openDialog(dialog, dialogClass) {
    dialog.showModal();
    dialog.classList.add('show');
}
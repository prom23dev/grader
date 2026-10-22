function toggleMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('active');
}
$(document).ready(function() {
    $("#phone").mask("+ 7 (999) 999 99 99");
});
function closeDialog(dialog) {
    dialog.close();
    dialog.classList.remove('show');
}

function openDialog(dialog, dialogClass) {
    dialog.showModal();
    dialog.classList.add('show');
}
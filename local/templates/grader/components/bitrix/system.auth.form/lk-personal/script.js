$(document).ready(function() {
    const leaveDialog = document.getElementById('leaveDialog');
    const leaveAgreeButton = document.getElementById('leaveYes');

    $(document).on('click', '.account__menu-item.account__exit a', function (e) {
        e.preventDefault();
        showLeaveModal();
        $('.leave-dialog__choose-block button').on('click', function (b) {
            leaveAgreeButton.addEventListener('click', () => {
                $('input[name=logout_butt]').click();
                // window.location.replace('/');
            })
        });
    });
});

function showLeaveModal(){
    document.getElementById('leaveDialog').showModal()
    document.getElementById('leaveDialog').classList.add('show')
}
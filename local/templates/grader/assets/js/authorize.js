// Авторизация/регистрация
let time = 100;

function startTimer() {
    function updateTime() {
        if (time > 0) {
            $('.recover-code__annotation').text(`Через ${time} секунд можно получить новый код`);
            time--;
        } else {
            $('.recover-code__annotation').text('Новый код доступен!');
            clearInterval(timer);
        }
    }

    let timer = setInterval(updateTime, 1000);
    updateTime();
}

const recoverCodeDialog = document.getElementById('recoverCodeDialog')
const registerDialog = document.getElementById('registrationDialog');
const authDialog = document.getElementById('authDialog')
const forgotPasswordDialog = document.getElementById('forgotPasswordDialog')
const changePasswordSuccess = document.getElementById('changePasswordSuccess')
const changePasswordDialog = document.getElementById('changePasswordDialog')
const callBackSuccess = document.getElementById('callBackSuccess')
const userNotExitDialog = document.getElementById('userNotExistDialog')
const failedLoginDialog = document.getElementById('failedLoginDialog')
const callBackDialog = document.getElementById('callBackDialog')
const urlPath = new URL(window.location.href)

let authRedirect = null;

function openAuth(redirectUrl = null) {
    authRedirect = redirectUrl || null;
    authDialog.showModal();
    authDialog.classList.add('show')
}

function closeDialog(dialog) {
    dialog.close();
    dialog.classList.remove('show');
}

function openDialog(dialog, dialogClass) {
    dialog.showModal();
    dialog.classList.add('show');
}

function closeRegistrationDialog() {
    closeDialog(registerDialog);
    openDialog(authDialog);
}

function closeAuthDialog() {
    closeDialog(authDialog);
}

function closeForgotPasswordDialog() {
    closeDialog(forgotPasswordDialog)
    openDialog(authDialog)
}

function closeRecoverDialog() {
    closeDialog(recoverCodeDialog)
    openDialog(authDialog)
}

function toggleRegisterDialog() {
    closeDialog(authDialog);
    openDialog(registerDialog);
}

function toggleForgotPasswordDialog() {
    closeDialog(authDialog)
    openDialog(forgotPasswordDialog)
}

function toggleRecoverCodeDialog() {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm.checkValidity()) {
        closeDialog(forgotPasswordDialog);
        // openDialog(recoverCodeDialog);
        startTimer();
    } else {
        forgotPasswordForm.reportValidity();
    }
}

function toggleMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('active');
}

function redirect(url) {
    window.location.href = url
}

$('#authForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: window.location.pathname,
        cache: false,
        contentType: false,
        processData: false,
        data: new FormData(this),
        success: function (response) {
            let html = "<div>" + response + "</div>";
            let error = $(html).find('#password-error').text();
            console.log(error)
            if (error) {
                openDialog(failedLoginDialog);
                // $('#password').addClass('error');
                // $('#password-error').text(error)
                // $('#password').on('change input', function (){ $('#password').removeClass('error'); $('#password-error').text('')})
            } else {
                

                if (window.location.href.includes('/reviews/')) {
                    setTimeout(() => {
                        closeAuthDialog();
                        var top = $('#review-text').offset().top;
                        $('body,html').animate({scrollTop: top}, 1000);
                    }, 1000);

                } else {
                    setTimeout(() => {
                        closeAuthDialog();
                        if (authRedirect) {
                            closeAuthDialog();
                            window.location.replace(authRedirect);
                            authRedirect = null;
                        } else {
                            window.location.replace('/personal/');
                        }
                        
                    }, 1000);
                }
            }
        }, error: function (request, error) {
            console.log(request, error)
        }
    });
})
$('#regForm').on('submit', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        type: "POST",
        url: window.location.pathname,
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        success: function (response) {
            // что возвращается в случае если пользователь существует и откуда возврат?
            setTimeout(() => {
                closeDialog(registerDialog)
            }, 1000);
            let html = '<div>' + response + '</div>';
            let error = $(html).find('#form-register-error').text();
            if (error) openDialog(userNotExitDialog)
            setTimeout(() => {
                closeDialog(userNotExitDialog);
            }, 5000);
        },
    });
})

$('#forgotPasswordForm').on('submit', function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        type: "POST",
        url: window.location.pathname,
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        success: function (response) {
            closeDialog(forgotPasswordDialog);
            openDialog(callBackSuccess)
        },
    });
})


if (urlPath.search.includes('change_password=yes')) {
    openDialog(changePasswordDialog)
}
$('#changePasswordForm').on('submit', function (e) {
    e.preventDefault();
    const newPassword = $("#new-password-input").val();
    const repeatPassword = $("#repeat-password-input").val();
    const note = $('.form-note')

    const errorMessage = $("#newPassword-error");
    let formData = new FormData(this);

    if (newPassword !== repeatPassword) {
        note.css('marginTop', '15px')
        $("#new-password-input").addClass('error')
        $("#repeat-password-input").addClass('error')
        event.preventDefault();
        errorMessage.text("Пароли не совпадают.").css('color', 'red');
    } else {
        note.css('marginTop', '0')
        errorMessage.text("");
        $("#new-password-input").removeClass('error')
        $("#repeat-password-input").removeClass('error')

        $.ajax({
            type: "POST",
            url: window.location.pathname,
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                closeDialog(changePasswordDialog);
                setTimeout(() => {
                    openDialog(changePasswordSuccess)
                }, 500);
                setTimeout(() => {
                    closeDialog(changePasswordSuccess)
                }, 4000);
            },
        });
    }

})

$('#callbackForm').on('submit', function (e) {
    e.preventDefault();
    let phone = $(this).find('#phone').val();
    $.post('/local/templates/grader/assets/ajax/addCallBack.php', {phoneNum: phone}, function (data) {
        setTimeout(() => {
            closeDialog(callBackDialog);
        }, 500);
    })
})

const successDialog = document.getElementById('success-send-review-dialog');
const failedDialog = document.getElementById('failed-send-review-dialog')
$('.reviews__send-review-form').on('submit', function (e) {
    e.preventDefault();
    let reviewText = $(this).find('textarea').val();
    $.post("/local/templates/grader/assets/ajax/createReview.php", {TEXT: reviewText}, function (data) {
        console.log(data)
        if (data === 'success') {
            openDialog(successDialog);
            $('#review-text').val('');
            localStorage.removeItem('review');
        } else {
            localStorage.setItem('review', reviewText)
            openDialog(failedDialog);
        }
    })
})

$(document).ready(function () {
    $("#reg-phone").mask("+ 7 (999) 999 99 99")
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const formattedDate = `${year}-${month}-${day}`;
    $("#reg-phone").on("focus", function () {
        const input = this;
        setTimeout(function () {
            input.setSelectionRange(0, 0);
        }, 0);
    });

    $("#reg-phone").on("click", function (e) {
        e.preventDefault();
        const input = this;
        setTimeout(function () {
            input.setSelectionRange(0, 0);
        }, 0);
    });

    if (document.getElementById('birthDate') !== null) {
        document.getElementById('birthDate').setAttribute('max', formattedDate);
        $('#birthDate').on('input change', function () {
            const inputValue = $(this).val();
            const inputDate = new Date(inputValue);
            const maxDate = new Date(formattedDate);

            if (inputDate > maxDate) {
                $(this).val('');
            }
        });
    }
})

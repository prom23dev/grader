$(document).ready(function() {
    openDialog(authDialog);
    $('#breadcrumbs').breadcrumbsGenerator({
        sitemaps: '#sitemaps',
        index_type: 'index.html'
    });
    $("#phone").mask("+ 7 (999) 999 99 99");
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const formattedDate = `${year}-${month}-${day}`;

    document.getElementById('birthDate').setAttribute('max', formattedDate);
    $('#birthDate').on('input change', function () {
        const inputValue = $(this).val();
        const inputDate = new Date(inputValue);
        const maxDate = new Date(formattedDate);

        if (inputDate > maxDate) {
            $(this).val('');
        }
    });

    const targetInput = document.querySelector('#password')

    $('.password-control').on('click', function () {
            if (targetInput.type === 'password') {
                targetInput.type = 'text';  // Показываем пароль
                $(this).html(`
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`
                );
            } else {

                targetInput.type = 'password';  // Скрываем пароль
                $(this).html(`
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1_6154)">
                        <path d="M9.9 4.24002C10.5883 4.0789 11.2931 3.99836 12 4.00003C19 4.00003 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19M14.12 14.12C13.8454 14.4148 13.5141 14.6512 13.1462 14.8151C12.7782 14.9791 12.3809 15.0673 11.9781 15.0744C11.5753 15.0815 11.1752 15.0074 10.8016 14.8565C10.4281 14.7056 10.0887 14.4811 9.80385 14.1962C9.51897 13.9113 9.29439 13.572 9.14351 13.1984C8.99262 12.8249 8.91853 12.4247 8.92563 12.0219C8.93274 11.6191 9.02091 11.2219 9.18488 10.8539C9.34884 10.4859 9.58525 10.1547 9.88 9.88003M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 3.96914 7.65663 6.06 6.06003L17.94 17.94Z" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1 1L23 23" stroke="#474A51" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_1_6154">
                            <rect width="24" height="24" fill="white"/>
                        </clipPath>
                    </defs>
                </svg>`
                );
            }
    });
    $('#otp_target').otpdesigner({
        typingDone: function (code) {
            console.log('Entered OTP code: ' + code);
        },
        length: 4,
        onlyNumbers: false,
        inputsClasses: 'some-class text-danger',
    });
});
let time = 100;
function startTimer() {
    function updateTime() {
        if (time > 0) {
            $('.recover-code__annotation').text(`Через ${time} секунд можно получить новый код`);
            time--;
        } else {
            $('.recover-code__annotation').add(`
                <a href='#'>
                    <button type='button'>Получить код</button>
                </a>
            `);
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
const userNotExitDialog = document.getElementById('userNotExistDialog')
const failedLoginDialog = document.getElementById('failedLoginDialog')
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
    window.location.replace('/front/');
}
function closeForgotPasswordDialog(){
    closeDialog(forgotPasswordDialog)
    openDialog(authDialog)
}
function closeRecoverDialog(){
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
function toggleRecoverCodeDialog(){
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm.checkValidity()) {
        closeDialog(forgotPasswordDialog);
        openDialog(recoverCodeDialog);
        startTimer();
    } else {
        forgotPasswordForm.reportValidity();
    }
}
// document.addEventListener('keydown', (event) => {
//     if (event.key === 'Escape') {
//         if (registerDialog.open) {
//             closeRegistrationDialog()
//         } else if (authDialog.open) {
//             closeAuthDialog();
//         }
//     }
// });



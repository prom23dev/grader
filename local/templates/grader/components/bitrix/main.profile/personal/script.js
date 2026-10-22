$(document).ready(function() {
    const editButtons = document.querySelectorAll('.edit-button');
    const saveButtonModal = document.getElementById('save-button-modal');
    const cancelButtonModal = document.getElementById('cancel-button-modal');
    const saveChangesButton = document.getElementById('save-changes');
    const discardChangesButton = document.getElementById('discard-changes');
    const saveOrDiscardBlock = document.getElementById('save-or-discard-block');
    const dialog = document.getElementById('changes-dialog');
    const inputs = document.querySelectorAll('.my-profile__item input');

    let isEditing = false;
    let initialValues = [];

// Функция для переключения редактирования
    function toggleEdit(enable) {
        inputs.forEach(input => {
            input.disabled = !enable;
        });
        saveOrDiscardBlock.style.display = enable ? 'flex' : 'none';
    }

    function updateSaveButtonState() {
        saveChangesButton.disabled = !hasChanges();
    }
// Функция для проверки, были ли изменения
    function hasChanges() {
        return Array.from(inputs).some((input, index) => {
            if (input.type === 'radio') {
                return input.checked !== initialValues[index].checked;
            }
            return input.value !== initialValues[index].value;
        });
    }
    editButtons.forEach(editButton => {
        editButton.addEventListener('click', () => {
            if (!isEditing) {
                initialValues = Array.from(inputs).map(input => ({
                    value: input.value,
                    checked: input.checked
                }));
                toggleEdit(true);
                isEditing = true;
            } else {
                if (hasChanges()) {
                    dialog.showModal();
                    dialog.style.display = 'flex';
                    dialog.style.flexDirection = 'column';
                    dialog.style.justifyContent = 'center';
                    dialog.style.opacity = 1
                } else {
                    toggleEdit(false);
                    isEditing = false;
                }
            }
        });
    });
    inputs.forEach(input => {
        input.addEventListener('input', updateSaveButtonState);
    });

// Обработчик для кнопки "Сохранить и выйти" для модального окна
    saveButtonModal.addEventListener('click', (e) => {
        e.preventDefault()
        toggleEdit(false);
        isEditing = false;
        dialog.close()
        dialog.style.display = 'none';
        dialog.style.opacity = 0;
        saveData()
    });

// Обработчик для кнопки "Отмена" для модального окна
    cancelButtonModal.addEventListener('click', () => {
        inputs.forEach((input, index) => {
            if (input.type === 'radio') {
                input.checked = initialValues[index].checked;
            } else {
                input.value = initialValues[index].value;
            }
        });
        toggleEdit(false);
        isEditing = false;
        dialog.close();
        dialog.style.display = 'none';
        dialog.style.opacity = 0;
    });

// Обработчик для кнопки "Сохранить"
    saveChangesButton.addEventListener('click', (e) => {
        e.preventDefault()
        toggleEdit(false);
        isEditing = false;
        //пишем ajax измененные данные
        saveData()
    });
// Обработчик если нажать "ESC" в модалке
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            inputs.forEach((input, index) => {
                if (input.type === 'radio') {
                    input.checked = initialValues[index].checked;
                } else {
                    input.value = initialValues[index].value;
                }
            });
            toggleEdit(false);
            isEditing = false;
            dialog.close();
            dialog.style.display = 'none';
            dialog.style.opacity = 0;
        }
    });

    function saveData() {
        let formData = new FormData();
        formData.append('lastName', $('#lastName').val())
        formData.append('firstName', $('#firstName').val())
        formData.append('middleName', $('#middleName').val())
        formData.append('userName', $('#userName').val())
        formData.append('birthDate', $('#birthDate').val())
        formData.append('gender',$('input[name="gender"]:checked').val());
        formData.append('phone', $('#phone').val())
        formData.append('email', $('#email').val())
        console.log('send')
        $.ajax({
            type: "POST",
            url: "/local/templates/grader/assets/ajax/editUsersInfo.php",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'json',
        })
    }

});

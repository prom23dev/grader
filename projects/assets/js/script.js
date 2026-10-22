$(document).ready(function () {
// CREATE_PROJECT
    const uploadFiles = [];
    $("#file").change(function () {
        if (window.FormData === undefined) {
            alert('В вашем браузере FormData не поддерживается')
        } else {
            var formData = new FormData();
            $.each($("#file")[0].files, function (key, input) {
                formData.append('file[]', input);
                uploadFiles.push(input)
            });

            $.ajax({
                type: "POST",
                url: '/upload.php',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    data.forEach(function (msg) {
                        if ($('.img-container img').length <= 5) {
                            $('.img-container').prepend(msg);
                        } else {
                            window.alert('Вы можете загрузить только до 5 фотографий')
                        }
                    });
                }
            });
        }
        showDeleteMarker()
    });
    $('#mg-send').on('click', () => {
        console.log(uploadFiles)
    })

    $(document).on('submit', '#create-project-form', function (e) {
        e.preventDefault()
        console.log(uploadFiles)
        var formData2 = new FormData()
        let location = {
            'region': $('#region-select').val(),
            'district': $('#district-select').val(),
            'settlement': $('#settlement-select').val()
        };
        formData2.append('location', JSON.stringify(location));
        formData2.append('street', $('#street').val())
        formData2.append('work-type', $('#work-type').val())

        formData2.append('dop-info', $('#dop-info').val())
        formData2.append('name', $('#name').val())
        formData2.append('phone', $('#phone').val())
        formData2.append('email', $('#email').val())
        $.each(uploadFiles, function (key, input) {
            formData2.append('files[]', input);
        });
        $.ajax({
            type: "POST",
            url: "/local/templates/grader/assets/ajax/createProject.php",
            cache: false,
            contentType: false,
            processData: false,
            data: formData2,
            dataType: 'json',
        })
            .done(function () {
                const successDialog = document.getElementById('success-dialog');
                successDialog.showModal();
                successDialog.classList.add('show');
                const handleCloseAndRedirect = ({currentTarget, target}) => {
                    const isClickedOnBackdrop = target === currentTarget;

                    if (isClickedOnBackdrop) {
                        currentTarget.close();
                        window.location.replace("/");
                    }
                };
                successDialog.addEventListener("click", handleCloseAndRedirect);


                $('.closeModal').on('click', function () {
                    successDialog.close();
                    window.location.replace("/");
                })
            })
            .fail(function () {
                const failedDialog = document.getElementById('failed-dialog');
                failedDialog.showModal();
                failedDialog.classList.add('show');
                failedDialog.addEventListener('close', () => failedDialog.classList.remove('show'), {once: true});

                const handleModalClick = ({currentTarget, target}) => {
                    const isClickedOnBackdrop = target === currentTarget;

                    if (isClickedOnBackdrop) {
                        currentTarget.close();
                    }
                };

                failedDialog.addEventListener("click", handleModalClick);
            });
    })
    $("#phone").mask("+ 7 (999) 999 99 99")
    $("#phone").on("focus", function () {
        const input = this;
        setTimeout(function () {
            input.setSelectionRange(0, 0);
        }, 0);
    });

    $("#phone").on("click", function (e) {
        e.preventDefault();
        const input = this;
        setTimeout(function () {
            input.setSelectionRange(0, 0);
        }, 0);
    });

    $("#work-type").select2({
        width: '100%',
        placeholder: 'Введите вид работ или введите свой вариант',
        tags: true,
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });

    $("#work-type").on('select2:open', function() {
        setTimeout(function() {
            $('.select2-container--open .select2-search__field').attr('placeholder', 'Введите вид работ или предложите свой вариант');
        }, 0);
    });

    const customWorkTypeDialog = document.getElementById('mdl_Item');
    $('#type').on('click', function (e) {
        e.preventDefault();
        $('#custom-work-type').val('');
        customWorkTypeDialog.showModal();
        customWorkTypeDialog.classList.add('show');
    });
    customWorkTypeDialog.addEventListener('close', () => customWorkTypeDialog.classList.remove('show'));
    $('#custom-work-type-save').on('click', function () {
        const customValue = $.trim($('#custom-work-type').val());
        if (!customValue) {
            return;
        }
        const newOption = new Option(customValue, customValue, true, true);
        $('#work-type').append(newOption).trigger('change');
        customWorkTypeDialog.close();
    });
// SUPPORT_PROJECT
    let summ = 100, payMethod = 'card';
    $.each($('.cost_container__option'), (i, el) => {
        $(el).on('click', function () {
            summ = $(el).text().replace('₽', '')
            $('#summ').val(summ)
        })
    });
    $('#summ').on('change', () => {
        // summ = $('#summ').val()+' ₽'
        summ = $('#summ').val()
    })


    $('.support-project__variant [name=pay]').on('click', function () {
        payMethod = $(this).val()
    });
    $(document).on('submit', '#support-form', function (e) {
        e.preventDefault()

        let name = $('#name').text() || $('#name').val(),
            phone = $('#phone').text() || $('#phone').val(),
            email = $('#email').text() || $('#email').val(),
            projectId = $('#project-id').val(),
            amount = parseInt(String($('#summ').val() || summ).replace(/\D/g, ''), 10);

        if (!amount) {
            window.alert('Укажите сумму перевода')
            return
        }

        if (!projectId) {
            window.alert('Не указан проект')
            return
        }

        const submitButton = $(this).find('[type=submit]').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "/local/templates/grader/assets/ajax/createTinkoffPayment.php",
            contentType: 'application/json',
            data: JSON.stringify({
                name: $.trim(name),
                phone: $.trim(phone),
                email: $.trim(email),
                amount: amount,
                payMethod: payMethod,
                projectId: projectId
            }),
            dataType: 'json'
        })
            .done(function (data) {
                if (data && data.paymentURL) {
                    window.location.href = data.paymentURL;
                    return;
                }
                window.alert('Не удалось создать платёж. Попробуйте ещё раз.');
                submitButton.prop('disabled', false);
            })
            .fail(function (xhr) {
                const error = xhr.responseJSON && xhr.responseJSON.error;
                window.alert(error || 'Не удалось создать платёж. Попробуйте ещё раз.');
                submitButton.prop('disabled', false);
            });
    })

    const inputField = document.getElementById('summ');
    const buttons = document.querySelectorAll('.cost_container__option');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            if (this.classList.contains('selected')) {
                this.classList.remove('selected');
            } else {
                buttons.forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');
            }
        });
    });
    if (inputField) {
        inputField.addEventListener('input', function () {
            buttons.forEach(button => button.classList.remove('selected'));
        });
    }

    function showDeleteMarker() {
        if (uploadFiles.length > 0) {
            $('.delete-button-container').css('display', 'flex')
        } else {
            $('.delete-button-container').css('display', 'none')
        }
    }

    $(document).on('click', ' .img-container input[type=button]', function () {
        let images = $('.img-container img');
        uploadFiles.pop();
        console.log(uploadFiles.length)
        if (images.length > 1) {
            $(images)[0].remove()
        }
        showDeleteMarker()
    })

    // Инициализация select2 для гео-селектов
    $("#region-select").select2({
        width: '100%',
        placeholder: 'Регион, Край, область',
        value: '',
        allowClear: false,
        tags: true,
        language: {
            noResults: function() {
                return "Ввести свой вариант";
            }
        },
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });

    // Инициализация селекта района как disabled
    $("#district-select").prop('disabled', true).select2({
        width: '100%',
        placeholder: 'Район',
        value: '',
        allowClear: false,
        tags: true,
        language: {
            noResults: function() {
                return "Добавить этот вариант";
            }
        },
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });

    // Инициализация селекта населенного пункта как disabled
    $("#settlement-select").prop('disabled', true).select2({
        width: '100%',
        placeholder: 'Населенный пункт',
        value: '',
        allowClear: false,
        tags: true,
        language: {
            noResults: function() {
                return "Добавить этот вариант";
            }
        },
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });

    $("#region-select").on('select2:open', function() {
        setTimeout(function() {
            $('.select2-container--open .select2-search__field').attr('placeholder', 'Введите ваш регион');
        }, 0);
    });

    $("#district-select").on('select2:open', function() {
        setTimeout(function() {
            $('.select2-container--open .select2-search__field').attr('placeholder', 'Введите ваш район или добавьте его вручную');
        }, 0);
    });

    $("#settlement-select").on('select2:open', function() {
        setTimeout(function() {
            $('.select2-container--open .select2-search__field').attr('placeholder', 'Введите ваш населенный пункт или добавьте его вручную');
        }, 0);
    });

    // Загрузка ГЕО данных из HL-блоков (GeoRegion/GeoDistrict/GeoSettlement)
    // через тот же эндпоинт, что и виджет выбора города на главной (map/get-geo.php).
    // Значение option — ID элемента HL-блока; select2 tags:true всё ещё
    // позволяет ввести вариант, которого нет в справочнике (id тега = текст),
    // такие значения резолвятся/создаются на сервере в createProject.php.
    let selectedRegion

    $.getJSON("/map/get-geo.php?type=regions", function (regions) {
        regions.forEach(region => {
            $('#region-select').append(`<option value="${region.id}">${region.name}</option>`)
        })

        // Обновление select2 после добавления опций
        $('#region-select').trigger('change');

        $("#region-select").on("change", function () {
            selectedRegion = $(this).val();
            if (!selectedRegion) {
                // Если регион не выбран, отключаем район и населенный пункт
                $('#district-select').prop('disabled', true).trigger('change');
                $('#settlement-select').prop('disabled', true).trigger('change');
                return;
            }

            // Включаем селект района
            $('#district-select').prop('disabled', false).trigger('change');

            $('#district-select').empty()
            $('#district-select').append(`<option value="" selected>Район</option>`)

            // Отключаем и очищаем населенный пункт
            $('#settlement-select').prop('disabled', true);
            $('#settlement-select').empty();
            $('#settlement-select').append(`<option value="" selected>Населенный пункт</option>`);
            $('#settlement-select').trigger('change');

            if (!/^\d+$/.test(selectedRegion)) {
                // Новый регион введён вручную (не выбран из справочника) —
                // списка районов для него ещё нет.
                $('#district-select').trigger('change');
                return;
            }

            $.getJSON("/map/get-geo.php?type=districts&region_id=" + encodeURIComponent(selectedRegion), function (districts) {
                districts.forEach(district => {
                    $('#district-select').append(`<option value="${district.id}">${district.name}</option>`)
                })
                $('#district-select').trigger('change');
            });
        })

        $("#district-select").on("change", function () {
            const selectedDistrict = $(this).val();
            if (!selectedRegion) return;

            if (!selectedDistrict) {
                // Если район не выбран, отключаем населенный пункт
                $('#settlement-select').prop('disabled', true).trigger('change');
                return;
            }

            // Включаем селект населенного пункта
            $('#settlement-select').prop('disabled', false).trigger('change');

            $('#settlement-select').empty();
            $('#settlement-select').append(`<option value="" selected>Населенный пункт</option>`);

            if (!/^\d+$/.test(selectedDistrict)) {
                // Новый район введён вручную — списка населённых пунктов для него ещё нет.
                $('#settlement-select').trigger('change');
                return;
            }

            $.getJSON("/map/get-geo.php?type=settlements&district_id=" + encodeURIComponent(selectedDistrict), function (settlements) {
                settlements.forEach(settlement => {
                    $('#settlement-select').append(`<option value="${settlement.id}">${settlement.name}</option>`);
                });
                $('#settlement-select').trigger('change');
            });
        });

    });

})

$(document).on('click', '.delete', function () {
    let url = $(this).data('url');
    let tableId = $(this).data('table');
    let refresh = $(this).data('refresh');
    let div_id = $(this).data('div');
    deleteConfirmation(url, tableId, refresh, div_id);
});

function deleteConfirmation(url, tableId, refresh = false, div_id = null) {
    window.swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this record",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.value) {
            window.swal.fire({
                title: "",
                text: "Please wait...",
                showConfirmButton: false,
                backdrop: true
            });

            window.axios.delete(url).then(response => {
                if (response.status === 200) {
                    window.swal.close();
                    $(tableId).DataTable().ajax.reload();
                    if (refresh) {
                        window.location.reload();
                    }
                    if (div_id != null) {
                        console.log(response);
                        $(div_id).html(response.data.html);
                    }
                    // Show toast message
                    window.toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                }
            }).catch(error => {
                window.swal.close();
                // Show toast message
                window.toast.fire({
                    icon: 'error',
                    title: error.response.data.message
                });
            });
        }
    });
}

function toastMessage(message = '', status = '') {
    status = status=='' ? 'error' : status;

    if (message=='')
        message = status=='error' ? 'Something went wrong' : 'Success';

    window.toast.fire({
        title: message,
        icon: status,
    });
}

$('body').on('click', '[data-act=ajax-modal]', function () {
    const _self = $(this);

    const content = $("#ajax_model_content");
    const spinner = $("#ajax_model_spinner");

    content.hide();
    spinner.show();

    var modalSize =
        typeof _self.attr("data-modal-size") === "undefined"
            ? "modal-lg"
            : _self.attr("data-modal-size");
    $(".modal-dialog").addClass(modalSize);

    $("#ajax_model").modal({backdrop: 'static'});
    $("#ajax_model_title").html(_self.attr('data-title'));
    var metaData = {};
    $(this).each(function () {
        $.each(this.attributes, function () {
            if (this.specified && this.name.match("^data-post-")) {
                var dataName = this.name.replace("data-post-", "");
                metaData[dataName] = this.value;
            }
        });
    });

    axios({
        method: _self.attr('data-method'),
        url: _self.attr('data-action-url'),
        data: metaData
    })
    .then(response => {
        spinner.hide();
        if (response.status === 200) {
            content.html(response.data).show();

            $("#ajax_model").modal("show");

            $('#imageInput').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview')
                            .attr('src', e.target.result)
                            .show();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        else {
            toastMessage();
        }

        $('.form-select-modal').select2({
            dropdownParent: $('.modal'),
            minimumResultsForSearch: Infinity,
            width: '100%',
        });

    }).catch(error => {
        spinner.hide();
        toastMessage(error.response.data.message);
    });
});

$('body').on('submit', '[data-form=ajax-form]', function(e) {
    e.preventDefault();
    const form = this;
    const confirm = $(form).data('confirm');

    if (confirm=='yes') {
        window.swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to submit this form?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, do it!"
        }).then((result) => {
            if (result.value) sendAjaxForm(form);
        });
    } else {
        sendAjaxForm(form);
    }
});

function sendAjaxForm(form) {
    const _self = $(form);
    const btn = _self.find('[data-button=submit]');
    const btnHtml = btn.html();
    const modal = _self.data('modal');
    const dt = _self.data('datatable');
    const redirect = _self.data('redirect');
    const html_div_id = _self.data('html-div-id');
    const show_div = _self.data('show-div');
    const hide_div = _self.data('hide-div');
    const refresh = _self.data('refresh');
    const formReset = _self.data('form-reset');

    btn.attr('disabled', 'disabled');
    btn.html(btnHtml + '&nbsp;&nbsp;<span class="spinner-border spinner-border-sm"></span>');

    axios({
        url: _self.attr('action'),
        method: _self.attr('method'),
        data: new FormData(_self[0]),
    })
    .then(response => {
        if (response.status == 200) {
            if (modal !== '') $(modal).modal('hide');
            toastMessage(response.data.message, 'success');
            if (formReset) {
                _self[0].reset();
            }
            if (refresh) {
                window.location.reload();
            }
            if (dt !== '') $(dt).DataTable().ajax.reload();
            if (redirect) {
                window.location.href = response.data.redirectUrl;
            }
            if (response.data.view) {
                $(html_div_id).html(response.data.view);
            }
            if (show_div || hide_div) {
                $(show_div).removeClass('d-none');
                $(hide_div).addClass('d-none');
                _self[0].reset();
            }
        }
        else toastMessage();
    })
    .catch(error => {
        console.log(error);
        toastMessage(error.response.data.message);
    })
    .finally(response => {
        btn.removeAttr('disabled');
        btn.html(btnHtml);
    });
}

// to show uploaded image
function addFileEventToLabel(file_id, label_id, preview_id) {
    // for board signature
    const label = document.getElementById(label_id),
        file_input = document.getElementById(file_id),
        preview = document.getElementById(preview_id);

    label.addEventListener("click", function (e) {
        if (file_input) {
            file_input.click();
        }
        e.preventDefault(); // prevent form default action sign_image_preview
    }, false);

    file_input.addEventListener('change', function () {
        const file = file_input.files[0];
        let reader = new FileReader();

        reader.addEventListener("load", function () {
            preview.src = reader.result;
        }, false);

        if (file) {
            reader.readAsDataURL(file);
            $('#upload-btn').show();
        }
    });
}

// to approve and disapprove user
$(document).on('click', '.approve, .disapprove', function () {
    let url = $(this).data('url');
    let tableId = $(this).data('table');
    let tr = $(this).closest('tr');
    let rowData = $(tableId).DataTable().row(tr).data();
    let action = $(this).hasClass('approve') ? 'active' : 'rejected';

    var data = {
        'id': rowData.id,
        'status': action,
    }

    processRequest(url, tableId, data);
});

// to process the request of approval and disapproval
function processRequest(url, tableId, formData) {
    var message = formData.status === 'active'
        ? `<label class="col-form-label">
                You want to approve this
            </label>`
        : `<label class="col-form-label">
                Please, provide disapproval reason:
            </label>
            <textarea name="rejected_reason" id="disapproval_reason" class="form-control" rows="3"></textarea>`;
    window.swal.fire({
        title: 'Are you sure?',
        icon: 'warning',
        html: message,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Yes",
    }).then((result) => {
        formData['rejected_reason'] = $('[name=rejected_reason]').val()
        if (result.value) {
            window.swal.fire({
                title: "",
                text: "Please wait...",
                showConfirmButton: false,
                backdrop: true
            });
            window.axios.post(url, formData).then(response => {
                if (response.status === 200) {
                    window.swal.close();
                    $(tableId).DataTable().ajax.reload();

                    // Show toast message
                    window.toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                }
            }).catch(error => {
                window.swal.close();
                // Show toast message
                window.toast.fire({
                    icon: 'error',
                    title: error.response.data.message
                });
            });
        }
    });
}

$(document).on("change", ".file-upload", function() {
    ! function(e) {
        if (e.files && e.files[0]) {
            var t = new FileReader;
            t.onload = function(e) {
                $(".profile-pic").attr("src", e.target.result)
                $("#remove_image").val(e.target.result);
                $('.close-button').removeClass('d-none');
            }, t.readAsDataURL(e.files[0])
        }
    }(this)
});

$(document).on("click", ".upload-button", function() {
    $(".file-upload").click()
});

$(document).on("click", ".close-button", function() {
    $('.close-button').addClass('d-none');
    $(".profile-pic").attr('src', "/assets/no_avatar.png");
    $("#remove_image").val('/assets/no_avatar.png');
});

$('.select2').select2();

$(document).on('click', '.request-confirmation', function () {
    let message = $(this).data('message');
    let btn_text = $(this).data('button-text');
    let method = $(this).data('method');
    let url = $(this).data('url');
    let data_table = $(this).data('table');
    let refresh_div = $(this).data('refresh-div');
    let status = $(this).data('status');
    window.swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: btn_text
    }).then((result) => {
        if (result.value) {
            window.swal.fire({
                title: "",
                text: "Please wait...",
                showConfirmButton: false,
                backdrop: true
            });

            window.axios({
                method: method,
                url: url,
                data: {
                    status: status,
                }
            }).then(response => {
                if (response.status === 200) {
                    window.swal.close();
                    if (data_table) {
                        $(data_table).DataTable().ajax.reload();
                    }
                    if (refresh_div) {
                        $(refresh_div).html('');
                    }
                    window.toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                }
            }).catch(error => {
                window.swal.close();
                window.toast.fire({
                    icon: 'error',
                    title: error.response.data.message
                });
            });
        }
    });
});


$('body').on('click', '#generate_password', function () {
    var random_string = Math.random().toString(36).slice(-8);
    $('#password').val(random_string);
    $('#password_confirmation').val(random_string);
});

$('body').on('click', '#copy_password', function () {
    document.getElementById("password").select();
    document.execCommand('copy');
});

$(document).on('click', '.reset_filters', function() {
    $(".select2").not('.reset-disable').val('').trigger('change');
    $('.toggle-date').val('');
});

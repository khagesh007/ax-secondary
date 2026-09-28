jQuery.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("body").append(`<div class="modal fade" id="expLoginModal" aria-labelledby="expLoginModalLabel" data-bs-backdrop="static"  data-bs-keyboard="false" tabindex="-1" aria-modal="true" role="dialog">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header card-header pb-2">
                    <h5 class="modal-title" id="expLoginModalLabel">Login again?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger shadow" role="alert">
                        <strong> Attention! </strong> <b>Your session has been expired.</b>
                    </div>
                    <form id="ajaxLoginForm">

                        <div class="mb-3">
                            <label for="username" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="login_email" placeholder="Enter email">
                        </div>
                        <input type="hidden" name="_token" id="login-csrf-token"  />
                        <div class="mb-3">

                            <label class="form-label" for="login-password">Password</label>
                            <div class="position-relative auth-pass-inputgroup mb-3">
                                <input type="password" name="password" class="form-control pe-5" placeholder="Enter password" id="login-password">

                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" name="remember" type="checkbox"  id="auth-remember-check">
                            <label class="form-check-label" for="auth-remember-check">Remember me</label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" id="ajaxLoginButton" class="btn btn-success w-100" type="submit" style="background-color: #d3a188;border-color: #d3a188;
    color: #333;">Sign In</button>
                            <a href="/" type="button" class="btn btn-default w-100" type="submit">Back to homepage</a>
                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>`);
function checkSession() {

    $.get(sessionCheckUrl, {}, result => {
        if (!result.status) {
            $("#login-csrf-token").val(result.token);
            $("#expLoginModal").modal('show');
        } else {
            $("#expLoginModal").modal('hide');
        }
    }, 'json');
};
// setInterval(function () {
//     checkSession()
// }, 900000);
$(document).ajaxError(function(event,xhr,options,exc){
    if(xhr.status == 401 || xhr.status == 419){
        checkSession();
    }
});
let ajaxLoginRoute = ajaxLoginUrl;
$(document).on('submit', '#ajaxLoginForm', function (event) {
    $("#ajaxLoginButton").prop('disabled', true);
    event.preventDefault();
    let formData = new FormData($(this)[0]);
    $.ajax({
        url: ajaxLoginRoute,
        type: 'POST',
        dataType: 'json',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.status == '1') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': result.token
                    }
                });
                $("input[name='_token']").val(result.token);
                showToastify(result.msg, "success")
                $("#expLoginModal").modal('hide');
                $("#ajaxLoginButton").prop('disabled', false);
            } else {
                $("#ajaxLoginButton").prop('disabled', false);
                showToastify(result.msg, "error")
            }
        },
        error: function (jqXhr, json, errorThrown) {
            $("#saveBtn").prop('disabled', false);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function (index, item) {
                    showToastify(item[0], "error")
                })
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "error")
            }
        },
    });
});

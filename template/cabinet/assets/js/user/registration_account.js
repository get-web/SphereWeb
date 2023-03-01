$(document).ready(function () {

    get_captcha();
    $(".captcha_img").on('click', function (e) {
        get_captcha();
    });

    function get_captcha() {
        $.ajax({
            type: "POST",
            url: "/captcha",
            async: true,
        }).success(function (data) {
            $(".captcha_img").attr("src", data);
        });
    }

    $("form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "/registration/account",
            data: $(this).serialize(),
            dataType: "json",
            encode: true,
        }).success(function (data) {
            if (data.ok) {
                notify_success(data.message);
            } else {
                notify_error(data.message)
            }
        });
    });
});

$('#new_account_word').on('click', function () {
    $.ajax({
        type: "POST",
        url: "/generation/account",
        encode: true,
    }).success(function (data) {
        $("input[name*='login']").val(data);
    });
});

$('#new_password_word').on('click', function () {
    $.ajax({
        type: "POST",
        url: "/generation/password",
        encode: true,
    }).success(function (data) {
        $("input[name*='password']").val(data);
    });
});
 
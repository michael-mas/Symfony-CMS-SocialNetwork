$(document).ready(function() {
    // add class out to group, if control is focused or has value
    $('.form-control').each(function() {
        if ($(this).val())
            $(this).parent().addClass('out');
    });
    $('.form-control').focus(function() {
        $(this).parent().addClass('out');
    }).blur(function() {
        if(!$(this).val())
            $(this).parent().removeClass('out');
    });

    // show password
    $('.show-password').click(function() {
        if($(this).text() == 'visibility_off') {
            $(this).html('visibility');
            $(this).prev().attr('type', 'text');
        } else {
            $(this).html('visibility_off');
            $(this).prev().attr('type', 'password');
        }
    });

    // add checked class to label if checkbox is checked
    $('#remember_me').change(function() {
        if ($(this).is(':checked')) {
            $('#rememberMe').addClass('checked');
        } else {
            $('#rememberMe').removeClass('checked');
        }
    });
});

$(function() {
    $('#cb_use_all_wares').click(function() {
        if ($(this).is(':checked')) {
           $('#select_wares').addClass('hidden'); 
        }
        else {
            $('#select_wares').removeClass('hidden');
        }
    });
});

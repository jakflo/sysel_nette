$(function() {
    if ($('#form_save').length > 0) {    
        CommonForms.refill_forms($('input, select'), $('#form_save').text());
    }
    
    $('button.order_by_button').click(function(){
        var name = $(this).attr('data-name');
        $('button.order_by_button').removeClass('marked');
        $(this).addClass('marked');
        $("input[name='order_by']").val(name);
        $('#curr_page').val('1');
        $('#orders_list_filter').submit();
    });
    
    $('#orders_list_filter input, #orders_list_filter select').change(function() {
        $('#curr_page').val('1');
    });
});

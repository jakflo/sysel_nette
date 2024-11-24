$(function(){
    var fce = new CommonDom();
    $('.rename_item_button').click(function() {
        fce.showDiv(this, 'rename_div_');
    });
    
    $('.rename_item_cancel').click(function() {
        var id = $(this).attr('data-id');
        fce.hideDiv(this, 'rename_div_');
        $(".rename_item_button[data-id='" + id + "']").removeClass('hidden');
    });
    
    $('.reset_item_area').click(function() {
        var id = $(this).attr('data-id');
        var old_value = $("input[name='old_value'][data-id='" + id + "']").val();
        $("input[name='area'][data-id='" + id + "']").val(old_value);
    });
});

$(function(){
    var fce = new CommonDom();
    
    $('.rename_item_lot_button').click(function() {
        fce.showDiv(this, 'rename_div_');
    }); 
    
    $('.rename_itemLot_cancel').click(function() {
        var id = $(this).attr('data-id');
        fce.hideDiv(this, 'rename_div_');
        $(".rename_item_lot_button[data-id='" + id + "']").removeClass('hidden');
    });
    
    var forms = new CommonForms();
    forms.setDateFilters();
    
});

$(function(){
    var fce = new CommonDom();
    
    $('.rename_butt').click(function() {
        fce.showDiv(this, 'rename_div_');
    }); 
    
    $('.rename_warehouse_cancel').click(function() {
        var id = $(this).attr('data-id');
        fce.hideDiv(this, 'rename_div_');
        $(".rename_butt[data-id='" + id + "']").removeClass('hidden');
    });
    
});



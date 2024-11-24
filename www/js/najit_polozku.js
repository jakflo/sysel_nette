var row_id = 1;

function add_row(return_dom = false) {
    $('table#item_list').append($('#item_template table tr').clone());
    var new_row = $('table#item_list tr.item.new');
    $(new_row).removeClass('new').attr('data-id', row_id);
    $(new_row).find('select').attr('name', 'items[' + row_id + '][item_id]');
    $(new_row).find('input').attr('name', 'items[' + row_id + '][item_amount]');
    var remove_butt = $(new_row).find('button.remove_item');
    $(remove_butt).attr('data-id', row_id);
    $(remove_butt).click(function() {
        del_row($(remove_butt));
    });    
    row_id++;
    if (return_dom) {
        return $(new_row);
    }
}

function del_row(button_dom) {    
    if ($('tr.item').length > 2) {
        var id = $(button_dom).attr('data-id');        
        $('tr.item[data-id="'+id+'"]').remove();        
    }
}

$(function() {
    var fce = new CommonForms();
    
    $('#add_item').click(function() {
        add_row();
    });
    
    $('#ware_select_all').change(function() {
        if (!$('#ware_select_all').is(':checked')) {
            $('input.ware_list_cb').prop('checked', false);
            $('#ware_list_cont').removeClass('hidden');
        }
        else {
            $('#ware_list_cont').addClass('hidden');            
        }
    });
    
    //load form    
    var saved_form = JSON.parse(atob($('#saved_form').text().trim()));
    var saved_ware_list = JSON.parse(atob($('#saved_ware_list').text().trim()));    
    fce.set_checkboxes_by_array($('input.ware_list_cb'), saved_ware_list);
    fce.refill_forms($('#ware_select_all'), $('#saved_form').text());
    var items_form = saved_form.items;
    if (items_form !== undefined && Object.keys(items_form).length > 0) {
        $.each(items_form, function(k, v) {
            var new_row = add_row(true);
            $(new_row).find('input').val(v.item_amount);
            fce.set_select($(new_row).find('select'), v.item_id);            
        });        
    }
    else {
        $('#ware_try_single_no, #ware_select_all').prop('checked', true);
        $('#ware_list_cont').addClass('hidden');
        add_row();        
    }    
});


$(function(){
    //detail action
    if ($('#form_save').length > 0) {    
        forms_fce.refill_forms($('input.itemlist_filter, select.itemlist_filter'), $('#form_save').text());
    }
    
    $('button.order_by_button').click(function(){
        var name = $(this).attr('data-name');
        $('button.order_by_button').removeClass('marked');
        $(this).addClass('marked');
        $("input[name='order_by']").val(name);
        $('#curr_page').val('1');
        $('#filter_form').submit();
    });
    
    $('.itemlist_filter').change(function() {
        $('#curr_page').val('1');
    });    
    //
    
    //brief action
    function fill_max_items(it_select_dom) {
        var url = $('#ajax_dir').text() + '/max_items.php';
        var w_id = $(it_select_dom).attr('data-w_id');
        var it_id = $(it_select_dom).val();
        if (w_id === 'empty') {
            w_id = $('#w_id_empty').val();
            var span = $('label[for="item_amount_w_empty"] span');
        }
        else {
            var span = $('label[for="item_amount_w_' + w_id + '"] span');
        }
        $.post(url, {w_id: w_id, it_id: it_id}, function(data, stat) {
            $(span).text(data.trim());
        });                
    }
    
    $('select.item_id').change(function() {
        fill_max_items(this);        
    });
    
    $('#w_id_empty').change(function() {
        var it_select_dom = $('select[data-w_id="empty"]');
        if ($(it_select_dom).val() !== '0') {
            fill_max_items(it_select_dom);
        }
    });
    
    var add_item_saved_form = $('#add_item_saved_form').text();
    if (add_item_saved_form !== undefined && add_item_saved_form.length > 0) {
        load_add_item_form(add_item_saved_form);
    }
    
    function load_add_item_form(base64_post_data) {
        var decoded = JSON.parse(atob(base64_post_data));
        var encoded_data = btoa(JSON.stringify(decoded.data));
        if (decoded.empty_ware) {
            forms_fce.refill_forms($('#add_item_to_empty_row td select, #item_amount_w_empty'), encoded_data);
            fill_max_items('#add_item_to_empty_row td select.item_id');
        }
        else {
            var w_id = decoded.data.w_id;
            var tr = $('tr.add_item_row[data-w_id="' + w_id + '"]');
            forms_fce.refill_forms($(tr).find('select'), encoded_data);
            forms_fce.refill_forms($(tr).find('input.item_amount'), encoded_data);
            fill_max_items($(tr).find('select.item_id'));
        }        
    }
});



class CommonDom {
    showDiv = (button, id_prefix) => {
        var id = $(button).attr('data-id');
        $('#' + id_prefix + id).removeClass('hidden');
        $(button).addClass('hidden');
    };
    
    hideDiv = (button, id_prefix, hide_button = false) => {
        var id = $(button).attr('data-id');
        $('#' + id_prefix + id).addClass('hidden');
        if (hide_button) {
            $(button).addClass('hidden');
        }
    }
}

class CommonForms {
    setDateFilters = () => {
        var conditionChanged = (conditionSelectDom) => {
            var value_2_name = $(conditionSelectDom).attr('data-value2-name');
            var value_2_input = $("input[name='" + value_2_name + "']");
            
            if ($(conditionSelectDom).val() === 'between') {
                $(value_2_input).removeClass('hidden');
            } else {
                $(value_2_input).addClass('hidden').val('');
            }
        };
        
        $('.date_filter').each((key, date_item) => {
            $(date_item).find('select').change(function() {
                conditionChanged($(this));
            });
            conditionChanged($(date_item).find('select'));
        });
    }
}

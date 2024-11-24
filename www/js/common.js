class CommonDom {
    showDiv = (button, id_prefix) => {
        var id = $(button).attr('data-id');console.log($(button).prop('data-id'));
        $('#' + id_prefix + id).removeClass('hidden');
        $(button).addClass('hidden');
    };
    
    hideDiv = (button, id_prefix, hide_button = false) => {console.log(2);
        var id = $(button).attr('data-id');
        $('#' + id_prefix + id).addClass('hidden');
        if (hide_button) {
            $(button).addClass('hidden');
        }
    }
}

class CommonForms {
    refill_forms = (domlist, data) => {
        //data = asoc. pole prevedene do JSON a zakodovane v BASE 64
        data = JSON.parse(atob(data.trim()));
        var inst = this;
        $(domlist).each(function(k, v){
            var name = $(v).attr('name');            
            if (data[name] !== undefined) {
                switch($(v).prop("tagName")) {
                    case 'INPUT':
                        var type = $(v).attr('type');
                        if (type === 'checkbox') {
                            $(v).prop('checked', true);                            
                        }
                        else if (type === 'radio') {
                            if ($(v).val() == data[name]) {
                                $(v).prop('checked', true);                                
                            }
                        }
                        else {
                            $(v).val(data[name]);
                        }
                        break;
                    case 'SELECT':
                        inst.set_select($(v), data[name]);
                        break;
                }
            }                
        });            
    };
    
    set_select = (dom, value) => {
        var opts = $(dom).find('option');
        $(opts).removeAttr('selected');
        $(opts).each(function(k, v) {
            if ($(v).attr('value') === value) {
                $(v).attr('selected', '');
            }            
        });
    };
    
    set_radio = (domlist, value) => {
        $(domlist).prop('checked', false);
        $(domlist).each(function(k, v) {
            if ($(v).val() === value) {
                $(v).prop('checked', true);
            }
        });
    };
    
    set_checkboxes_by_array = (domlist, namearray) => {
        $(domlist).prop('checked', false);
        $(domlist).each(function(k, v) {
            if (namearray.indexOf($(v).attr('name')) !== -1) {
                $(v).prop('checked', true);                
            }
        });
    };
}


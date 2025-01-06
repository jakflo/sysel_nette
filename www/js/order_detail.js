$(function() {
    var current_state = $("input[name='current_state']").val();
    var state_select = $("select[name='new_status']");
    var submit_button = $("input[name='sent']");
    
    var showHint = function(current_state, new_state) {
        $('#change_status_hints span').addClass('hidden');
        $('#change_status_hints span.change-' + current_state + '-to-' + new_state).removeClass('hidden');
    };
    
   $(state_select).change(function() {
       $(submit_button).removeAttr('disabled');
       if ($(this).val() == current_state) {
           $(submit_button).attr('disabled', '');
       }
       
       showHint(current_state, $(this).val());
   });
   
   $('#cancel_button').click(function() {
       $(state_select).val(current_state);
       $(submit_button).attr('disabled', '');
       showHint(current_state, current_state);
   });
   
   $("input[name='cancel_selection']").click(function() {
       $('input.prefered_warehouses_cb').each((key, cb) => {
           $(cb).prop('checked', false);
       });
   });
   
});

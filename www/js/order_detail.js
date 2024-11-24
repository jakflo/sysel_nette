$(function() {
   $('#status_select').change(function() {
       var old_stat = $('#old_status').val();
       var subm_butt = $('#subm_stat_change');
       $(subm_butt).removeAttr('disabled');
       if ($(this).val() == old_stat) {
           $(subm_butt).attr('disabled', '');
       }
   }); 
});

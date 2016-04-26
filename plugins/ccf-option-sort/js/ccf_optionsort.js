jQuery(document).ready(function($) {        
  var itemList = $('.sortable');
  itemList.sortable({
  	placeholder: "ui-state-highlight" ,
    update: function(event, ui) {
      input_target = "#input_"+ui.item.parent().attr("id");
      $(input_target).val(ui.item.parent().sortable('toArray').toString());      
      return; 
      }
  }); 
});	
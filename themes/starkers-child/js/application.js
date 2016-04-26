var DLN = window.DLN || {};
DLN.Behaviors = {
};
DLN.LoadBehavior = function(context){
  if(context === undefined){
    context = jQuery(document);
}
context.find("*[data-behavior]").each(function(){
  var that = jQuery(this);
  var behaviors = that.attr('data-behavior');
  jQuery.each(behaviors.split(" "), function(index,behaviorName){
    try {
      var BehaviorClass = DLN.Behaviors[behaviorName];
      var initializedBehavior = new BehaviorClass(that);
    }
    catch(e){
      // No Operation
    }
  });
 });
};
DLN.onReady = function(){
  DLN.LoadBehavior();
};
jQuery(document).ready(function($){
  DLN.onReady();
});
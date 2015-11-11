jQuery(document).ready(function(){

    var droppable = jQuery('.droppable');
    var draggable = jQuery('.draggable');

    draggable.draggable();

    droppable.droppable({
        drop : function (event,ui){
            jQuery(ui.draggable).detach().css({top: 0,left: 0}).appendTo(this);
        }
    });
});
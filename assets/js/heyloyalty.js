jQuery(document).ready(function(){

    var dropItem = jQuery('.droppable');
    var dragItem = jQuery('.draggable');
    var hlContainer = jQuery('.hl-container');


    dropItem.droppable({
        accept : ".draggable",
        drop : function (event,ui){
            if(jQuery(this).find('.draggable').length <= 0) {
                jQuery(ui.draggable).detach().css({top: 0, left: 0}).appendTo(this);
            }
        },
        out: function (event,ui) {
            jQuery('.draggable',this).detach().css({top:0,left:0}).appendTo('.hl-container');
            console.log(jQuery('.draggable').data('name'));
        },
        over: function (event,ui){

        }

    });
});
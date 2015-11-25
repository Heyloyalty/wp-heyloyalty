jQuery(document).ready(function(){

    var dropItem = jQuery('.droppable');
    var dragItem = jQuery('.draggable');
    var hlContainer = jQuery('.hl-container');
    var hidden = jQuery('#mapped-fields');
    var mappedFields = [];
    var removedFields = [];


    dropItem.droppable({
        accept : ".draggable",
        drop : function (event,ui){
            if(jQuery(this).find('.draggable').length <= 0) {
                jQuery(ui.draggable).detach().css({top: 0, left: 0}).appendTo(this);
                var keyValue = jQuery(this).data('name')+"="+jQuery(ui.draggable).text();
                mappedFields.push(keyValue);
                hidden.val(mappedFields);
                jQuery(ui.draggable).on('click',function(){
                    var keyValue = jQuery(this).parent().data('name')+"="+jQuery(this).text();
                    var index = mappedFields.indexOf(keyValue);
                    removedFields = (index > -1) ? mappedFields.splice(index,1) : mappedFields;
                    hidden.val(mappedFields);
                    jQuery(this).detach().css({top:0,left:0}).appendTo('.hl-container');
                });
            }
        }
    });
});
jQuery(document).ready(function($) {

    var hlContainer = jQuery('.hl-container');

    jQuery('#hl_lists').on('change',function(){

        jQuery.post(
            HLajax.ajaxurl,
            {
                action : "hl-ajax-submit",
                handle : "getListForMapping",
                listID : jQuery(this).val()
            },
            function(response) {
                jQuery('.hl-container .draggable').remove();
                jQuery('.wp-container .draggable').remove();
                jQuery.each(response.fields,function(key,value){
                    if(value.format == 'text' || value.format == 'date' || value.format == 'number') {
                        hlContainer.append('<div class="draggable" data-name"'+value.name+'"><label>' + value.name + '</label><img class="map-cancel" style="float:right; margin:3px 3px;" src="/wp-content/plugins/wp-heyloyalty/assets/img/badge_cancel_32.png"/>');
                    }
                });
                jQuery('.draggable').draggable({
                    revert:'invalid',
                    drag:function(event,ui){

                    }
                });
            }
        );
    });

});
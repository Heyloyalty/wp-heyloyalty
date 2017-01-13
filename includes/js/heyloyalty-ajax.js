jQuery(document).ready(function($) {

    /**
     * Ajax call.
     * Populates a div with fields form a Heyloyalty list.
     */
    var hlContainer = jQuery('.hl-container');

    jQuery('#hl_lists').on('change',function(){

        jQuery.post(
            HLajax.ajaxurl,
            {
                action : "hl-ajax-submit", //hook Admin.php listen for.
                handle : "getListForMapping", //method ajax_handler in Admin.php handles this.
                listID : jQuery(this).val() //gets listId for a heyloyalty list.
            },
            function(response) {
                jQuery('.hl-container .draggable').remove(); //clear elements before populate.
                jQuery('.wp-container .draggable').remove(); //clear elements before populate.
                
                jQuery.each(JSON.parse(response.response).fields,function(key,value){
                       hlContainer.append('<div class="draggable" data-name="'+value.name+'" data-format="'+value.format+'"><label>' + value.name + '</label><img class="map-cancel" style="float:right; margin:3px 3px;" src="/wp-content/plugins/wp-heyloyalty/includes/img/badge_cancel_32.png"/><div class="format"><span style="font-size:11px">'+"format: "+value.format+'</span></div>');
                });
                /** end populate **/
                
                //set draggable on elements
                jQuery('.draggable').draggable({
                    revert:'invalid',
                    drag:function(event,ui){
                    }
                });
            }
        );
    });
    /** end ajax call **/

});

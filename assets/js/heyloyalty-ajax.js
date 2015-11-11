jQuery(document).ready(function($) {

    jQuery('#hl_lists').on('change',function(){

        jQuery.post(
            HLajax.ajaxurl,
            {
                action : "hl-ajax-submit",
                handle : "getListForMapping",
                listID : jQuery(this).val()
            },
            function(response) {
                console.log(response);
            }
        );
    });

});
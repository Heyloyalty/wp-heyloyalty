jQuery(document).ready(function () {

    var dropItem = jQuery('.droppable');
    var dragItem = jQuery('.draggable');
    var hlContainer = jQuery('.hl-container');
    var hidden = jQuery('#mapped-fields');
    var mappedFields = [];
    var removedFields = [];
    var keyFormatValue = ''; //string container for mapped field


    /**
     * Drop event handler for heyloyalty fields.
     */
    dropItem.droppable({
        accept: ".draggable",
        drop: function (event, ui) {
            if (jQuery(this).find(dragItem).length <= 0) {

                var format = jQuery(ui.draggable).data('format');
                var hl_name = jQuery(ui.draggable).data('name');
                var wp_name = jQuery(this).data('name');

                //remove css when hl field is dropped onto wp field.
                jQuery(ui.draggable).detach().css({top: 0, left: 0}).appendTo(this);
                jQuery(ui.draggable).find('span').remove();

                //if multi choice add new element to hl container;
                if(format == 'multi') {
                    hlContainer.append('<div class="draggable" data-name="'+hl_name+'" data-format="'+format+'"><label>'+hl_name+'</label><img class="map-cancel" style="float:right; margin:3px 3px;" src="/wp-content/plugins/wp-heyloyalty/assets/img/badge_cancel_32.png"/><div class="format"><span style="font-size:11px">'+"format: "+format+'</span></div>');

                    //set draggable on elements
                    jQuery('.draggable').draggable({
                        revert:'invalid',
                        drag:function(event,ui){
                        }
                    });
                }

                //when a wp and hl fields is mapped, get values and hl format into string container
                keyFormatValue = wp_name + "=" + format + "=" + hl_name;
                mappedFields.push(keyFormatValue);

                //update mapped fields on hidden field.
                hidden.val(mappedFields);

                /**
                 * Click event.
                 * When a heyloyalty field is inside the wordpress user field it can be removed by clicking
                 * on the minus button.
                 */
                jQuery(ui.draggable).on('click', function () {

                    var format = jQuery(ui.draggable).data('format');

                    //when a wp and hl fields is mapped, get values and hl format into string container
                    keyFormatValue = jQuery(this).parent().data('name') + "=" + format + "=" + jQuery(ui.draggable).data('name');

                    //get index for field in array
                    var index = mappedFields.indexOf(keyFormatValue);

                    //remove field from array
                    removedFields = (index > -1) ? mappedFields.splice(index, 1) : mappedFields;

                    //update mapped fields on hidden field
                    hidden.val(removedFields);

                    //remove css from element and add it to the hl container.
                    jQuery(this).detach().css({top: 0, left: 0}).appendTo(hlContainer);
                    jQuery(this).find('.format').append('<span style="font-size:11px">format: ' + format + '</span>');
                });
            }
        }
    });
    /** end function **/

    /** field explore **/
    var container = jQuery('.fields-container');
    var infoContainer = jQuery('.fields-info-container');
    var fieldInfo = jQuery('.field-info');

    jQuery.each(fields,function(key,value){
        container.append('<div class="field" data-name="'+value.name+'" data-format="'+value.format+'"><label>' + value.name + '</label><div class="format"><span style="font-size:11px">'+"format: "+value.format+'</span></div>');
        var clone = fieldInfo.clone();
        clone.attr("id",value.name);
        jQuery.each(value.options,function(key,value){
            clone.append('<li>id: '+key+' name: '+value+'</li>').appendTo(infoContainer);
        });
    });

    container.find('.field').on('click',function(){
        var name = jQuery(this).data('name');
        jQuery('.field-info').hide();
        jQuery('#'+name).show();
    });
    
    /** end field explore **/
});
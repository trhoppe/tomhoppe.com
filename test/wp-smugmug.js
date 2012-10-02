/**
 * Handle:	wpSmugMugAdmin
 * Version: 0.0.1
 * Deps:	jQuery
 * Enqueue:	true
 */

var wpSmugMugAdmin = function() {}

wpSmugMugAdmin.prototype = {
    options           : {},
    generateShortCode : function() {
        var attrs = '';
        
        jQuery.each(this['options'], function(name, value){
            if (value != '') {
                attrs += ' ' + name + '="' + value + '"';
            }
        });
		
		return '[smugmug' + attrs + ']';
    },
    sendToEditor      : function(f) {
        var collection = jQuery(f).find("input[id^=wpSmugMug]:not(input:checkbox),input[id^=wpSmugMug]:checkbox:checked");
        
        var $this = this;
        
        collection.each(function () {
            var name = this.name.substring(10, this.name.length-1);

            if ( name == 'title' || name == 'description' ) {
				$this['options'][name] = escape(this.value);
			} else {
				$this['options'][name] = this.value;
			}
        });
        
      	var size = jQuery("#wpSmugMug_size").val();      	
      	$this['options']['size'] = size;
        
        var thumbsize = jQuery("input[name='wpSmugMug[thumbsize]']:checked").val();
        $this['options']['thumbsize'] = thumbsize;
        
        var link = jQuery("input[name='wpSmugMug[link]']:checked").val();
        $this['options']['link'] = link;
        
        if ( $this['options']['url'] == '' ) {
			alert("Please enter the URL to your SmugMug gallery RSS feed");
			return false;
		}
        
        send_to_editor(this.generateShortCode());
        return false;
    }
}

var wpSmugmugAdmin = new wpSmugMugAdmin();

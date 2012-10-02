<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<!-- closing tag for centering -->
</div>

<?php wp_footer(); ?>


<script type="text/javascript" src="http://www.tomhoppe.com/js/jquery.js"></script>
<script type="text/javascript" src="http://www.tomhoppe.com/js/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="http://www.tomhoppe.com/js/jquery.royalslider.js"></script>

<script type="text/javascript">
/* To do: Put all below styles in separate JS call and create namespace and helper functions for user agent and screen width sniffing */

function setDisplayBodyClass() {
	if($(window).width() < 1025) { $('html').removeClass('mini'); $('html').addClass('thin'); }
	if($(window).width() < 510) {  $('html').removeClass('thin'); $('html').addClass('mini'); }
	if($(window).width() > 1025) {  $('html').removeClass('thin'); $('html').removeClass('mini'); }
}
setDisplayBodyClass();

$(window).resize(function () { setTimeout(setDisplayBodyClass,500); });

if($(window).width() < 1025) { $(document).ready(function(){$("a.thickbox").colorbox({transition:"fade",speed:0,width:'90%',initialWidth:'100px',initialHeight:'100px',maxWidth:'1034px'}); }); }
else { $(document).ready(function(){$("a.thickbox").colorbox({transition:"fade",speed:0,initialWidth:'100px',initialHeight:'100px'})}); }

if ((navigator.userAgent.indexOf('AppleWebKit') != -1) && ((navigator.userAgent.indexOf('Mobile') != -1))) {
$(function(){
	window.scrollTo(0,1);
});
}

/* Inline for Now. To Do: Move this to external scripts file */
jQuery(document).ready(function($) {
  var win = $(window),
      w,
      cContent = $('#cboxLoadedContent'),
      currRs;
  $('.thumbwrap li a').on('click', function(e) {
      e.preventDefault();
      var self = this;

      

      $.colorbox({
          preloading: true,
          modal: true,
          transition: 'none',
          speed: 0,
          width: '100%',
          height: '100%',
          onComplete: function(e) {
            currRs = $('#cboxLoadedContent').find('.royalSlider').royalSlider({
              imageScaleMode: 'fit-if-smaller',
              keyboardNavEnabled: true,
              startSlideId: $(self).parent().index(),
              arrowsNavAutoHide:false,
              navigateByClick: false
            }).data('royalSlider');
          },
          onOpen: function(e) {
          	if(currRs != undefined) {

		        currRs.goToInstant($(self).parent().index());

		        currRs.ev.on('rsAfterSlideChange', function() {
				    $('.rsImg').show();
				});

		      }
          },
          inline:true,
          href:'#' + $(self).parents('.thumbwrap').attr('data-contentid')
      });

      return false;
  });

  win.resize( function() {
     updateLightboxSize();
  });

  // dynamic lightbox resizing
  function updateLightboxSize() {
       $.colorbox.resize({
           height: '100%',
           width: '100%'
       });
      if(currRs) {
        currRs.updateSliderSize();
      }
  }

});
</script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/trhoppe.json?callback=twitterCallback2&amp;count=6"></script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-4655438-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
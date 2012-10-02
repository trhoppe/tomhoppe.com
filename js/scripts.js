/* setup webmd namespace */
webmd = {
	m:{},
	p:{}
};

/* Used to hide the iPhone scroll bar after the page loads */
$(function(){
	window.scrollTo(0,1);
});


/* Used to set the body orientation so we can write rotation based CSS if needed. Target with body[orient="landscape"] {} */
window.onorientationchange = function() {
	switch(window.orientation){
	   case 0:
		   document.body.setAttribute('orient', 'portrait');
		   break;
	  case -90:
	  case 90:
		   document.body.setAttribute('orient', 'landscape');
		   break;
	}
}

/* Main menu navigation dropdown */
webmd.m.mainMenu = {

	init:function(){

		var self = this;

		this.menuButton = $('#main_menu_button');
		this.menu = $('#main_menu');
		this.closeButton = $('#main_menu_close');

		if (!(this.menuButton.length && this.menu.length && this.closeButton.length)){
			return;
		}

		this.hide(true);

		// Add close control
		this.closeButton.click(function(){
			self.hide();
			return false;
		});

		// Add open control
		this.menuButton.click(function(){
			self.toggle();
			return false;
		});

	},

	toggle:function(){
		if (this.menu.is(':visible')) {
			this.hide();
		} else {
			this.show();
		}
	},

	show:function(){
		this.menuButton.hide();
		this.menu.slideDown('fast');
	},

	hide:function(quick){
		if (quick) {
			this.menu.hide('',function(){$('#main_menu_button').show()});
		} else {
			this.menu.slideUp('fast',function(){$('#main_menu_button').show()});
		}
	}
};
$(function(){ webmd.m.mainMenu.init(); });



/* dropdown navigation control. Putting in scripts.js as this is used on pretty much every page of the site */
webmd.m.dropdown = {

	init:function(){

		var self = this;

		this.dropdownButton = $('.dropdown_wrap .header');

		if (!(this.dropdownButton.length)){
			return;
		}
		
		// Add toggle control
		this.dropdownButton.toggle(function(){
			$(this).parents('.dropdown').addClass('open');
			$(this).siblings('.dropdown_content').slideDown('fast');
			$(this).find('div').removeClass('arrow_down');
			$(this).find('div').addClass('arrow_up');
			return false;
		}, function() {
			$(this).parents('.dropdown').removeClass('open');
			$(this).siblings('.dropdown_content').slideUp('fast');
			$(this).find('div').removeClass('arrow_up');
			$(this).find('div').addClass('arrow_down');
			return false;
		});

	}
};
$(function(){ webmd.m.dropdown.init(); });



/*	webmd.useragent

	Requires: webmd.core.js

	Function: sniff out user agent string
	Notes: returns an object which has a type and a device
	
	type - 	"desktop" - The default value for a regular computer
			"tablet" - Currently defining this as ipad only. Will include android tablets
			"mobile" - The WebMD definition of mobile which is a webkit based mobile browser
			
	device - "pc" - Normal computer
			 "ipad" - Has iPad in the user agent string
			 "iphone" - Has iPhone in the user agent string
			 "android" - Has Android  in the user agent
*/
webmd.useragent = {
	
	ua:{
		type:'desktop',device:'pc'
	},

	init:function(){

		var self = this;
	
		if ((navigator.userAgent.indexOf('iPad') != -1)) {
			this.ua.type = 'tablet';
			this.ua.device = 'ipad';
		}
		if ((navigator.userAgent.indexOf('iPhone') != -1)) {
			this.ua.type = 'mobile';
			this.ua.device = 'iphone';
		}	
		if ((navigator.userAgent.indexOf('Android') != -1)) {
			this.ua.type = 'mobile';
			this.ua.device = 'android';
		}
		
		document.documentElement.className += this.ua.device;
		
	},
	get:function(){
		var self = this;
		return this.ua.device;	
	}
};
/* Initilizing now so this can be put on the document before HTML starts to load */
webmd.useragent.init();
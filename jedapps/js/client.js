Joomla.apps = {};
Joomla.apps.active = [];
Joomla.apps.view = "dashboard";
Joomla.apps.id = 0;
Joomla.apps.ordering = "";
Joomla.apps.fonturl = 'http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic';
Joomla.apps.cssfiles = [
	'components/com_apps/views/dashboard/css/japps.css',
	'components/com_apps/views/dashboard/css/jquery.jscrollpane.css',
];
Joomla.apps.jsfiles = [
	'components/com_apps/views/dashboard/js/jquery.jscrollpane.min.js',
	'components/com_apps/views/dashboard/js/jquery.mousewheel.js',
	'components/com_apps/views/dashboard/js/jquery.japps.js'
];

Joomla.loadweb = function(url) {
	if ('' == url) { return false; }

	url += '&product='+apps_product+'&release='+apps_release+'&dev_level='+apps_dev_level;

	jQuery('html, body').animate({ scrollTop: 0 }, 0);
	if (jQuery('#myTabContent').length) {
		jQuery('#appsloading')
			.css("top", jQuery('#myTabContent').position().top - jQuery(window).scrollTop())
			.css("left", jQuery('#myTabContent').position().left - jQuery(window).scrollLeft())
			.css("width", jQuery('#myTabContent').width())
			.css("height", jQuery('#myTabContent').height());
		jQuery.event.trigger("ajaxStart");
	}

	jQuery.ajax({
		url: url,
		dataType: 'jsonp',
		callbackParameter: "jsoncallback",
		timeout: 20000,
		success: function (response) {
			jQuery('#web-loader').hide();
			jQuery('#jed-container').html(response.data);
			if (jQuery('#myTabContent').length) {
				jQuery.event.trigger("ajaxStop");
			}
		},
		fail: function() {
			jQuery('#web-loader').hide();
			jQuery('#web-loader-error').show();
			if (jQuery('#myTabContent').length) {
				jQuery.event.trigger("ajaxStop");
			}
		},
		complete: function() {
			if (Joomla.apps.ordering !== "") {
				jQuery('#com-apps-ordering').prop("selectedIndex", Joomla.apps.ordering);
			}
			Joomla.apps.slider();
			Joomla.apps.clicker();
			Joomla.apps.clickforlinks();
			if(jQuery('#joomlaapsinstallatinput')) {
				jQuery('#joomlaapsinstallatinput').val(apps_installat_url);
			}
			if (jQuery('#myTabContent').length) {
				jQuery.event.trigger("ajaxStop");
			}
		},
		error: function(request, status, error) {
			if (request.responseText) {
				jQuery('#web-loader-error').html(request.responseText);
			}
			jQuery('#web-loader').hide();
			jQuery('#web-loader-error').show();
			if (jQuery('#myTabContent').length) {
				jQuery.event.trigger("ajaxStop");
			}
		}
	});
}

Joomla.webpaginate = function(url, target) {
	jQuery('#web-paginate-loader').show();
	
	jQuery.get(url, function(response) {
		jQuery('#web-paginate-loader').hide();
		jQuery('#'+target).html(response.data);
	}, 'jsonp').fail(function() { 
		jQuery('#web-paginate-loader').hide();
		//jQuery('#web-paginate-error').hide();
	});	
}

Joomla.installfromwebexternal = function(redirect_url) {
	var redirect_confirm = confirm('You will be redirected to the following link to complete the registration/purchase - \n'+redirect_url);
	if(true == redirect_confirm) {
		return true;
	}
	return false;
}

Joomla.installfromweb = function(install_url, name) {
	if ('' == install_url) {
		alert("This extension cannot be installed via the web. Please visit the developer's website to purchase/download.");
		return false;
	}
	jQuery('#install_url').val(install_url);
	jQuery('#uploadform-web-url').text(install_url);
	if (name) {
		jQuery('#uploadform-web-name').text(name);
		jQuery('#uploadform-web-name-label').show();
	} else {
		jQuery('#uploadform-web-name-label').hide();
	}
	jQuery('#jed-container').slideUp(300);
	jQuery('#uploadform-web').show();
}

Joomla.installfromwebcancel = function() {
	jQuery('#uploadform-web').hide();
	jQuery('#jed-container').slideDown(300);
}

Joomla.installfromwebajaxsubmit = function() {
	var tail = '&view='+Joomla.apps.view;
	if (Joomla.apps.id) {
		tail += '&id='+Joomla.apps.id;
	}
	
	if (jQuery('#com-apps-searchbox').val()) {
		var value = encodeURI(jQuery('#com-apps-searchbox').val().toLowerCase().replace(/ +/g,'_').replace(/[0-9]/g,'').replace(/[^a-z0-9-_]/g,'').trim());
		tail += '&filter_search='+value;
	}

	var ordering = Joomla.apps.ordering;
	if (ordering !== "" && jQuery('#com-apps-ordering').val()) {
		ordering = jQuery('#com-apps-ordering').val();
	}
	if (ordering) {
		tail += '&ordering='+ordering;
	}
	Joomla.loadweb(apps_base_url+'index.php?format=json&option=com_apps'+tail);
}

Joomla.apps.clickforlinks = function () {
	if (Joomla.apps.view == 'extension') {
		Joomla.apps.view = 'category';
		Joomla.apps.id = jQuery('div.breadcrumbs a.transcode').slice(-1).attr('href').replace(/^.+[&\?]id=(\d+).*$/, '$1');
	}
	jQuery('a.transcode').each(function(index) {
		var ajaxurl = jQuery(this).attr('href');
		(function() {
			var ajax_url = ajaxurl;
			var el = jQuery('a.transcode')[index];
			jQuery(el).live('click', function(event){
				Joomla.apps.view = ajax_url.replace(/^.+[&\?]view=(\w+).*$/, '$1');
				if (Joomla.apps.view == 'dashboard') {
					Joomla.apps.id = 0;
				}
				else {
					Joomla.apps.id = ajax_url.replace(/^.+[&\?]id=(\d+).*$/, '$1');
				}
				event.preventDefault();
				Joomla.loadweb(apps_base_url + ajax_url);
			});
		})();
		jQuery(this).attr('href', '#');
	});
}

jQuery(document).ready(function() {
	var link = jQuery('#myTabTabs a[href="#web"]').get(0);
	jQuery(link).closest('li').click(function (event){
		Joomla.apps.initialize();
	});
});

Joomla.apps.initialize = function() {
	if (jQuery('#myTabContent').length) {
		jQuery('<div id="appsloading"></div>')
			.css("background", "rgba(255, 255, 255, .8) url('"+apps_base_url+"components/com_apps/views/dashboard/css/ajax-loader.gif') 50% 15% no-repeat")
			.css("top", jQuery('#myTabContent').position().top - jQuery(window).scrollTop())
			.css("left", jQuery('#myTabContent').position().left - jQuery(window).scrollLeft())
			.css("width", jQuery('#myTabContent').width())
			.css("height", jQuery('#myTabContent').height())
			.css("position", "fixed")
			.css("z-index", "1000")
			.css("opacity", "0.80")
			.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
			.css("filter", "alpha(opacity = 80)")
			.appendTo('#myTabContent');
		jQuery('#appsloading').ajaxStart(function() {
			jQuery(this).show();
		}).ajaxStop(function() {
			jQuery(this).hide();
		});
	}

	Joomla.loadweb(apps_base_url+'index.php?format=json&option=com_apps&view=dashboard');
	
	Joomla.apps.clickforlinks();
	
	jQuery('#com-apps-searchbox').live('keypress', function(event){
		if(event.which == 13) {
			Joomla.installfromwebajaxsubmit();
		}
	});

	jQuery('#com-apps-ordering').live('change', function(event){
		Joomla.apps.ordering = jQuery(this).prop("selectedIndex");
		Joomla.installfromwebajaxsubmit();
	});
	
	if (apps_installfrom_url != '') {
		Joomla.installfromweb(apps_installfrom_url);
	}

}

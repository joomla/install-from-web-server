Joomla.apps = {};
Joomla.apps.active = [];
Joomla.apps.view = "dashboard";
Joomla.apps.id = 0;
Joomla.apps.ordering = "";

Joomla.loadweb = function(url) {
	if ('' == url) { return false; }

	jQuery.get(url, function(response) {
		jQuery('#web-loader').hide();
		jQuery('#jed-container').html(response.data);
	}, 'jsonp')
	.fail(function() { 
		jQuery('#web-loader').hide();
		jQuery('#web-loader-error').show();
	})
	.complete(function() {
		Joomla.apps.slider();
		Joomla.apps.clicker();
		Joomla.apps.clickforlinks();
		if (Joomla.apps.ordering !== "") {
			jQuery('#com-apps-ordering').prop("selectedIndex", Joomla.apps.ordering);
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

Joomla.installfromweb = function(install_url, name) {
	if ('' == install_url) {
		alert("This extension cannot be installed via the web. Please visit the developer's website to purchase/download.");
		return false;
	}
	jQuery('#install_url').val(install_url);
	jQuery('#uploadform-web-url').text(install_url);
	jQuery('#uploadform-web-name').text(name);
	jQuery('#jed-container').slideUp(300);
	jQuery('#uploadform-web').show();
}

Joomla.installfromwebcancel = function() {
	jQuery('#uploadform-web').hide();
	jQuery('#jed-container').slideDown(300);
}

Joomla.installfromwebajaxsubmit = function() {
	if (Joomla.apps.view == 'extension') {
		Joomla.apps.view = 'category';
		Joomla.apps.id = jQuery('div.breadcrumbs a.transcode').slice(-1).attr('href').replace(/^.+[&\?]id=(\d+).*$/, '$1');
	}
	var tail = '&view='+Joomla.apps.view;
	if (Joomla.apps.id) {
		tail += '&id='+Joomla.apps.id;
	}
	
	if (jQuery('#com-apps-searchbox').val()) {
		var value = encodeURI(jQuery('#com-apps-searchbox').val().toLowerCase().replace(/ +/g,'_').replace(/[0-9]/g,'').replace(/[^a-z0-9-_]/g,'').trim());
		tail += '&filter_search='+value;
	}

	var ordering = Joomla.apps.ordering;
	if (ordering !== "") {
		ordering = jQuery('#com-apps-ordering').val();
	}
	tail += '&ordering='+ordering;
	Joomla.loadweb(apps_base_url+'index.php?format=json&option=com_apps'+tail);
}

Joomla.apps.clickforlinks = function () {
	jQuery('a.transcode').live('click', function(event){
		ajax_url = jQuery(this).attr('href');
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
}

jQuery(document).ready(function() {
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

});

Joomla.loadweb = function(url) {
	if ('' == url) { return false; }

	jQuery.get(url, function(response) {
		jQuery('#web-loader').hide();
		jQuery('#jed-container').html(response.data);
	}, 'jsonp').fail(function() { 
		jQuery('#web-loader').hide();
		jQuery('#web-loader-error').show();
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

jQuery(document).ready(function() {
	Joomla.loadweb(apps_base_url+'index.php?format=json&option=com_apps&view=dashboard');
	
	jQuery('a.transcode').live('click', function(event){
		ajax_url = jQuery(this).attr('href');
		event.preventDefault();
		Joomla.loadweb(apps_base_url + ajax_url);
	});
});

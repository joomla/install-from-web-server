jQuery(document).ready(function(){
	jQuery(".com-apps-sidebar ul.com-apps-list li").click(function(event){
		event.preventDefault();
		if(jQuery(this).hasClass("active")){
			jQuery(this).removeClass("active");
			jQuery(this).find("ul").stop(true,true).slideUp(300);		
		}
		else{
			jQuery(this).closest("ul").find(" > li.active").find("ul").stop(true,true).slideUp(300);
			jQuery(this).closest("ul").find(" > li").removeClass("active");
			jQuery(this).addClass("active");
			jQuery(this).find("ul").stop(true,true).slideDown(300);
		
		}
	})
	jQuery("ul.nav-tabs li").click(function(){

		setTimeout(function(){jQuery('.scroll-pane').jScrollPane({
			autoReinitialise: true,
			mouseWheelSpeed: 10
		})},10);
	});	
	jQuery(".com-apps-advanced-search > a").click(function(){
		jQuery(this).closest(".com-apps-advanced-search").toggleClass("active");
		return false;
	})
})



jQuery(document).ready(function(){
	
	jQuery( ".grid-view" ).live("click",function() {
		jQuery( ".items" ).removeClass("list-view-container");	
		jQuery( ".items" ).addClass("grid-view-container");	
	});
	jQuery( ".list-view" ).live("click",function() {
		jQuery( ".items" ).removeClass("grid-view-container");	
		jQuery( ".items" ).addClass("list-view-container");	
        
        jQuery(".list-view-container .row-fluid .item").each(function(){
            jQuery(this).find("p.rating").insertAfter(jQuery(this).find('h4'));
            jQuery(this).find(".item-type").insertAfter(jQuery(this).find('h4'));
            
            var height = jQuery(this).height() - 10;
            jQuery(this).find('h4').css("height",height);
            jQuery(this).find('h4').css("padding-top", (jQuery(this).height() - jQuery(this).find('h4').find('a').height()-10)/2);
            
            jQuery(this).find('.rating').css('margin-top', (jQuery(this).height() - jQuery(this).find('.rating').height())/2);
            jQuery(this).find('ul.item-type').css('margin-top', (jQuery(this).height() - jQuery(this).find('ul.item-type').height())/2);
        })
	});
	jQuery('select').chosen({
		disable_search_threshold : 10,
		allow_single_deselect : true
	});
	
	<!--radio-->
	var repRadioCount = 0;
	
	var countRadio = jQuery("input[type=radio]").length;
	
	jQuery.fn.extend({
		replaceRadio: function () {
			repRadioCount++;
			
			if(jQuery(this).attr("name")===undefined){
				jQuery(this).attr("name","radio_"+repRadioCount)
			}
			if(jQuery(this).attr("checked")=="checked"){
				var radioHTML = "<div rel='"+jQuery(this).attr("name")+"' class='radiobutton checked'><div class='point'></div></div>"
			}
			else{
				var radioHTML = "<div rel='"+jQuery(this).attr("name")+"' class='radiobutton'><div class='point'></div></div>"
			}
			
			jQuery(this).hide();
			jQuery(this).addClass("parsed-radio");
			jQuery(this).before(radioHTML);
		}
	});				
	for(var i=0; i<countRadio; i++){
		jQuery("input[type=radio]").eq(i).replaceRadio();
	}	
	jQuery(".radiobutton").live("click",function(event){
		var rel = jQuery(this).attr("rel");
		if(rel != ""){
			jQuery(".radiobutton[rel|='"+rel+"']").removeClass("checked");
			jQuery("input[name|='"+rel+"']").removeAttr("checked");
		}
		jQuery(this).addClass("checked");
		jQuery(this).next("input[type=radio]").attr("checked","checked");
		
	})	
	jQuery("input[type=radio]").live("click",function(event){
		jQuery(this).prev(".radiobutton").trigger("click");
	})	

	<!--radio-->
	<!--check-->
	var repCheckCount = 0;
	
	var countCheck = jQuery("input[type=checkbox]").length;
	
	jQuery.fn.extend({
		replaceCheck: function () {
			repCheckCount++;
			
			if(jQuery(this).attr("name")===undefined){
				jQuery(this).attr("name","radio_"+repCheckCount)
			}
			if(jQuery(this).attr("checked")=="checked"){
				var checkHTML = "<div rel='"+jQuery(this).attr("name")+"' class='checkbox checked'><div class='check'></div></div>"
			}
			else{
				var checkHTML = "<div rel='"+jQuery(this).attr("name")+"' class='checkbox'><div class='check'></div></div>"
			}
			
			jQuery(this).hide();
			jQuery(this).addClass("parsed-check");
			jQuery(this).before(checkHTML);
		}
	});				
	for(var i=0; i<countCheck; i++){
		jQuery("input[type=checkbox]").eq(i).replaceCheck();
	}	
	jQuery(".checkbox").live("click",function(event){
		if(jQuery(this).hasClass("checked")){
			jQuery(this).removeClass("checked")
			jQuery(this).next("input[type=checkbox]").removeAttr("checked");
		
		}	
		else{
			jQuery(this).addClass("checked")
			jQuery(this).next("input[type=checkbox]").attr("checked","checked");				
		}
	})	
	jQuery("input[type=checkbox]").live("click",function(event){
		jQuery(this).prev(".checkbox").trigger("click");
	})	

	
	jQuery(document).bind("DOMNodeInserted",function(){
				
		var newCountRadio = jQuery("input[type=radio]").length;
		
		var newCountCheck = jQuery("input[type=checkbox]").length;
		
		if( newCountRadio != countRadio){

			var newCountRadio = jQuery("input[type=radio]:not('.parsed-radio')").length;
			
			for(var i=0; i<newCountRadio; i++){
				jQuery("input[type=radio]:not('.parsed-radio')").eq(i).replaceRadio();
			}	
		}
		
		if( newCountCheck != countCheck){

			var newCountCheck = jQuery("input[type=checkbox]:not('.parsed-check')").length;
			
			for(var i=0; i<newCountCheck; i++){
				jQuery("input[type=checkbox]:not('.parsed-check')").eq(i).replaceCheck();
			}	
		}
		
		return false;
	})		
});
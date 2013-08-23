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
	
	<!--selectbox-->		
	var repSelectCount = 0;
	
	var count = jQuery("select").length;
	
	jQuery("body").append("<ul id='unique-custom-pane'></ul>")

	jQuery.fn.extend({
		openInPosition: function (x,y,z,rel) {
			var elemHeight = jQuery(this).height();
			
			var leftSpace = jQuery(window).height()+jQuery(window).scrollTop()-y-z;
			
			jQuery(this).addClass("openInPosition");
			
			jQuery(this).stop(true,true).slideDown(300);

			
			jQuery(this).attr("rel",rel);

			if(leftSpace<elemHeight){
				jQuery(this).css({"left":x,"top":y-elemHeight+1});
			}
			else{
				jQuery(this).css({"left":x,"top":y+z+1+5});
			}
			
			
		}
	});	
	
	jQuery.fn.extend({
		closeInPosition: function () {
			jQuery(this).stop(true,true).slideUp(300);
			
			jQuery(this).removeClass("openInPosition");
			
			jQuery(this).html();
			
			jQuery(this).attr("rel","none");
			
		}
	});	
	
	jQuery.fn.extend({
		closeSelect: function () {
			jQuery(this).removeClass("open-select");
			
			jQuery("#unique-custom-pane").closeInPosition();
		}
	});

	jQuery.fn.extend({
		replaceSelect: function () {
			if(jQuery(this).hasClass("parsed-select")){

				var selectedValue = jQuery(this).val();
				
				var str = "option[value='"+selectedValue+"']"
				
				selectedValue = jQuery(this).find(str).html();
				
				if(typeof selectedValue === 'undefined'){
					selectedValue="Select";
				}	
				
				jQuery(this).prev("ul.custom-select").find("a.custom-select-anchor").html(selectedValue)
				
				return false;						
			}
			else {
				var optsCount = jQuery(this).find("option").length;
				
				for(var i=0; i<optsCount; i++){
					var val = jQuery(this).find("option").eq(i).attr("value");
					
					jQuery(this).find("option").eq(i).attr("value",val);
				}
				
				repSelectCount++;
				
				var selectedValue = jQuery(this).val();
				
				var str = "option[value='"+selectedValue+"']";
				
				selectedValue = jQuery(this).find(str).html();
				
				if(typeof selectedValue === 'undefined'){
					selectedValue="Select";
				}
				
				var selectHTML = "<ul class='custom-select' id='customSelect_"+repSelectCount+"'>" + 
				
				"<li><a class='custom-select-anchor' href='javascript:void(0)'>" + selectedValue + "</a></li></ul>";
				
				unique_data = "customSelect_" +repSelectCount;
				
				jQuery(this).addClass(unique_data);
				
				jQuery(this).addClass("parsed-select");
				
				jQuery(this).hide();
				
				jQuery(this).before(selectHTML);
				
				return false;
			}
		}
	});
	
	jQuery("ul.custom-select > li > a").live("click",function(){
		var elem = jQuery(this).closest(".custom-select");
		
		if(elem.hasClass("open-select")){
		
			elem.removeClass("open-select");
			
			jQuery("#unique-custom-pane").closeInPosition();
			
		}
		else{
		
			jQuery(".open-select").closeSelect();
			
			elem.addClass("open-select");
			
			var startPosX = elem.offset().left;
			
			var startPosZ = elem.height();
			
			var startPosY = elem.offset().top;
			
			var elemID = elem.attr("id");
			
			var optionsHTML = elem.next("select").html();
			
			optionsHTML = optionsHTML.replace(/<option/g,"<li");
			
			optionsHTML = optionsHTML.replace(/option>/g,"li>");

			optionsHTML = optionsHTML.replace(/value=/g,"rel=");
			
			jQuery("#unique-custom-pane").html(optionsHTML);
			
			jQuery("#unique-custom-pane").openInPosition(startPosX,startPosY,startPosZ, elemID);		
			
		}
		return false;
	})
	
	jQuery("#unique-custom-pane li").live("click",function(event){
		var optionValue = jQuery(this).attr("rel");
		
		var selectClass = jQuery(this).parent().attr("rel");
		
		selectClass = "."+selectClass;
		
		var str = "option[value='"+optionValue+"']";
		
		jQuery(selectClass).find(str).attr("selected","selected");
		
		jQuery(selectClass).trigger("change");
	})
	
	jQuery("select").live("change",function(event){
		jQuery(this).replaceSelect();
	})
	jQuery("option").live("change",function(event){
		jQuery(this).replaceSelect();
	})
	
	jQuery("html").live("click", function(event){
		if(event.target.className == "custom-select-anchor"){
								
		}
		else{
			jQuery(".custom-select").closeSelect();
		}
	})
	
	for(var i=0; i<count; i++){
		jQuery("select").eq(i).replaceSelect();
	}	

	<!--selectbox-->
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
		
		var newCount = jQuery("select").length;
		
		var newCountRadio = jQuery("input[type=radio]").length;
		
		var newCountCheck = jQuery("input[type=checkbox]").length;
		
		
		jQuery(".mod-languages select").eq(i).replaceSelect();
		

		if( newCount != count){
		
			var newCount = jQuery("select:not('.parsed-select')").length;
			
			for(var i=0; i<newCount; i++){
				jQuery("select:not('.parsed-select')").eq(i).replaceSelect();
			}	
		}
		
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
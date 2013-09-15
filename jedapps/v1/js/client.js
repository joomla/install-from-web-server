Joomla.apps = {
	active: [],
	view: "dashboard",
	id: 0,
	ordering: "",
//	fonturl: 'http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic',
	cssfiles: [],
	jsfiles: [],
	list: 0,
	desc: {
		full: [],
		grid: [],
		list: []
	},
	adminform: {}
};

Joomla.loadweb = function(url) {
	if ('' == url) { return false; }

	if (Joomla.apps.adminform.hasOwnProperty('action')) {
		jQuery('#adminForm').attr('action', Joomla.apps.adminform.action);
	}

	var pattern1 = new RegExp(apps_base_url);
	var pattern2 = new RegExp("^index\.php");
	if (!(pattern1.test(url) || pattern2.test(url))) {
		window.open(url, "_blank");
		return false;
	}

	url += '&product='+apps_product+'&release='+apps_release+'&dev_level='+apps_dev_level+'&list='+Joomla.apps.list;

	jQuery('html, body').animate({ scrollTop: 0 }, 0);
	if (jQuery('#myTabContent').length) {
		jQuery('#appsloading')
			.css("top", jQuery('#myTabContent').position().top - jQuery(window).scrollTop())
			.css("left", jQuery('#myTabContent').position().left - jQuery(window).scrollLeft())
			.css("width", jQuery('#myTabContent').width())
			.css("height", jQuery('#myTabContent').height());
		jQuery.event.trigger("ajaxStart");
	}

	Joomla.apps.desc = {
		full: [],
		grid: [],
		list: []
	};

	jQuery.ajax({
		url: url,
		dataType: 'jsonp',
		cache: true,
		jsonpCallback: "jedapps_jsonpcallback",
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
			Joomla.apps.active = [];
			jQuery("ul.com-apps-list a.active").each(function(index){
				Joomla.apps.active.push(jQuery(this).attr("href").replace(/^.+[&\?]id=(\d+).*$/, '$1'));
			});
			jQuery(".com-apps-sidebar ul.com-apps-list li a.active").each(function(index, value) {
				if (!jQuery(value).hasClass('selected')) {
					jQuery(value).parent().children("ul").css("display", "block");
				}
			});
			if(jQuery('#joomlaapsinstallatinput')) {
				jQuery('#joomlaapsinstallatinput').val(apps_installat_url);
			}
			Joomla.apps.clickforlinks();
			Joomla.apps.clicker();
			jQuery('.item-description').each(function(index){
				Joomla.apps.setDescription('grid', index, jQuery(this));
			});
			if (Joomla.apps.list) {
				jQuery(".list-view").click();
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

Joomla.apps.setDescription = function(type, index, value) {
	if (Joomla.apps.desc[type].length <= index) {
		var desc;
		if (Joomla.apps.desc.full.length <= index) {
			desc = jQuery(value).html();
			(function() {
				var description = desc;
				Joomla.apps.desc.full.push(description);
			})();
		}
		jQuery(value).html(Joomla.apps.desc.full[index]);var i = 0;
		while (jQuery(value)[0].scrollHeight >  jQuery(value).height()) {
			jQuery(value).html(jQuery(value).html().replace(/\s[^\s]+$/, '...'));
		}
		jQuery(value).html(jQuery(value).html().replace(/\W+$/, '...'));
		desc = jQuery(value).html();
		(function() {
			var description = desc;
			Joomla.apps.desc[type].push(description);
		})();
	}
	jQuery(value).html(Joomla.apps.desc[type][index]);
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
		var value = encodeURI(jQuery('#com-apps-searchbox').val().toLowerCase().replace(/ +/g,'_').replace(/[^a-z0-9-_]/g,'').trim());
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
	jQuery('a.transcode').each(function(index, value) {
		var ajaxurl = jQuery(this).attr('href');
		(function() {
			var ajax_url = ajaxurl;
			jQuery(value).live('click', function(event){
				var pattern1 = new RegExp(apps_base_url);
				var pattern2 = new RegExp("^index\.php");
				if (pattern1.test(ajax_url) || pattern2.test(ajax_url)) {
					Joomla.apps.view = ajax_url.replace(/^.+[&\?]view=(\w+).*$/, '$1');
					if (Joomla.apps.view == 'dashboard') {
						Joomla.apps.id = 0;
					}
					else if (Joomla.apps.view == 'category') {
						Joomla.apps.id = ajax_url.replace(/^.+[&\?]id=(\d+).*$/, '$1');
					}
					event.preventDefault();
					Joomla.loadweb(apps_base_url + ajax_url);
				}
				else {
					event.preventDefault();
					Joomla.loadweb(ajax_url);
				}
			});
		})();
		jQuery(this).attr('href', '#');
	});
	if (Joomla.apps.view.toLowerCase() == 'extension' &&
	    jQuery('div.form-actions button').length) {
		Joomla.apps.adminform.action = jQuery('#adminForm').attr('action');
		jQuery('#adminForm').attr('action', jQuery("#joomlaapsinstallfrominput").val());
	}	
}

Joomla.apps.initialize = function() {
	if (jQuery('#myTabContent').length) {
		jQuery('<div id="appsloading"></div>')
			.css("background", "rgba(255, 255, 255, .8) url('../media/jui/img/ajax-loader.gif') 50% 15% no-repeat")
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
			Joomla.apps.initiateSearch();
			return false;
		}
	});

	jQuery('#myTabTabs li').live('click', function(event){
		if (jQuery(this).find('a[href="#web"]').length && jQuery("#joomlaapsinstallfrominput").val()) {
			jQuery('#adminForm').attr('action', jQuery("#joomlaapsinstallfrominput").val());
		}
		else if (Joomla.apps.adminform.hasOwnProperty('action')) {
			jQuery('#adminForm').attr('action', Joomla.apps.adminform.action);
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

Joomla.apps.initiateSearch = function() {
	Joomla.apps.view = 'dashboard';
	Joomla.apps.active = [];
	Joomla.installfromwebajaxsubmit();
}

Joomla.apps.slider = function() {
	jQuery(".com-apps-sidebar ul.com-apps-list li a.selected").each(function(index, value) {
		jQuery(value).parent().addClass("active");
		jQuery(value).parent().children("ul").show();
	});
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
	jQuery(".com-apps-advanced-search .cancel").click(function(){
		jQuery( ".com-apps-advanced-search" ).removeClass("active");	
	})
	jQuery(document).mouseup(function (e)
	{
	    var container = jQuery(".com-apps-advanced-search");
	    if (container.has(e.target).length == 0)
	    {
			jQuery( ".com-apps-advanced-search" ).removeClass("active");
    	}
	});
}

Joomla.apps.clicker = function() {
	jQuery(".grid-view").live("click",function() {
		Joomla.apps.list = 0;
		jQuery(".items").each(function(index) {
			jQuery(this).removeClass('list-container');
			jQuery(this).addClass('grid-container');
		});
		jQuery(".grid-container .item").each(function(index){
			jQuery(this).find("h4").insertAfter(jQuery(this).find('.item-type')).css("height", "").css("padding-top", "");
			jQuery(this).find("p.rating").insertBefore(jQuery(this).find('.item-image'));
			jQuery(this).find('.rating').css('margin-top', "");
			jQuery(this).find('ul.item-type').css('margin-top', "");
			jQuery(this).find('.item-description').css("margin-top", "");
			Joomla.apps.setDescription('grid', index, jQuery(this).find('.item-description'));
		});
	});
	jQuery(".list-view").live("click",function() {
		Joomla.apps.list = 1;
		jQuery(".items").each(function(index) {
			jQuery(this).removeClass('grid-container');
			jQuery(this).addClass('list-container');
		});
		jQuery(".list-container .item").each(function(index){
			var h4 = jQuery(this).find('h4');
			var rating = jQuery(this).find("p.rating");
			var type = jQuery(this).find(".item-type");
			var desc = jQuery(this).find('.item-description');

			jQuery(rating).insertAfter(h4);
			jQuery(type).insertAfter(h4);

			var height = jQuery(this).height();
			jQuery(h4).css("height", height);
			jQuery(h4).css("padding-top", (height - jQuery(h4).find('a').height())/2);
            
			jQuery(rating).css('margin-top', (height - jQuery(rating).height())/2);
			jQuery(type).css('margin-top', (height - jQuery(type).height())/2);
			jQuery(desc).css("margin-top", (height - jQuery(desc).height())/2);
			Joomla.apps.setDescription('list', index, jQuery(desc));
		});
	});
}

/*!
 * jScrollPane - v2.0.17 - 2013-08-17
 * http://jscrollpane.kelvinluck.com/
 *
 * Copyright (c) 2013 Kelvin Luck
 * Dual licensed under the MIT or GPL licenses.
 */
!function(a,b,c){a.fn.jScrollPane=function(d){function e(d,e){function f(b){var e,h,j,l,m,n,q=!1,r=!1;if(P=b,Q===c)m=d.scrollTop(),n=d.scrollLeft(),d.css({overflow:"hidden",padding:0}),R=d.innerWidth()+tb,S=d.innerHeight(),d.width(R),Q=a('<div class="jspPane" />').css("padding",sb).append(d.children()),T=a('<div class="jspContainer" />').css({width:R+"px",height:S+"px"}).append(Q).appendTo(d);else{if(d.css("width",""),q=P.stickToBottom&&C(),r=P.stickToRight&&D(),l=d.innerWidth()+tb!=R||d.outerHeight()!=S,l&&(R=d.innerWidth()+tb,S=d.innerHeight(),T.css({width:R+"px",height:S+"px"})),!l&&ub==U&&Q.outerHeight()==V)return d.width(R),void 0;ub=U,Q.css("width",""),d.width(R),T.find(">.jspVerticalBar,>.jspHorizontalBar").remove().end()}Q.css("overflow","auto"),U=b.contentWidth?b.contentWidth:Q[0].scrollWidth,V=Q[0].scrollHeight,Q.css("overflow",""),W=U/R,X=V/S,Y=X>1,Z=W>1,Z||Y?(d.addClass("jspScrollable"),e=P.maintainPosition&&(ab||db),e&&(h=A(),j=B()),g(),i(),k(),e&&(y(r?U-R:h,!1),x(q?V-S:j,!1)),H(),E(),N(),P.enableKeyboardNavigation&&J(),P.clickOnTrack&&o(),L(),P.hijackInternalLinks&&M()):(d.removeClass("jspScrollable"),Q.css({top:0,left:0,width:T.width()-tb}),F(),I(),K(),p()),P.autoReinitialise&&!rb?rb=setInterval(function(){f(P)},P.autoReinitialiseDelay):!P.autoReinitialise&&rb&&clearInterval(rb),m&&d.scrollTop(0)&&x(m,!1),n&&d.scrollLeft(0)&&y(n,!1),d.trigger("jsp-initialised",[Z||Y])}function g(){Y&&(T.append(a('<div class="jspVerticalBar" />').append(a('<div class="jspCap jspCapTop" />'),a('<div class="jspTrack" />').append(a('<div class="jspDrag" />').append(a('<div class="jspDragTop" />'),a('<div class="jspDragBottom" />'))),a('<div class="jspCap jspCapBottom" />'))),eb=T.find(">.jspVerticalBar"),fb=eb.find(">.jspTrack"),$=fb.find(">.jspDrag"),P.showArrows&&(jb=a('<a class="jspArrow jspArrowUp" />').bind("mousedown.jsp",m(0,-1)).bind("click.jsp",G),kb=a('<a class="jspArrow jspArrowDown" />').bind("mousedown.jsp",m(0,1)).bind("click.jsp",G),P.arrowScrollOnHover&&(jb.bind("mouseover.jsp",m(0,-1,jb)),kb.bind("mouseover.jsp",m(0,1,kb))),l(fb,P.verticalArrowPositions,jb,kb)),hb=S,T.find(">.jspVerticalBar>.jspCap:visible,>.jspVerticalBar>.jspArrow").each(function(){hb-=a(this).outerHeight()}),$.hover(function(){$.addClass("jspHover")},function(){$.removeClass("jspHover")}).bind("mousedown.jsp",function(b){a("html").bind("dragstart.jsp selectstart.jsp",G),$.addClass("jspActive");var c=b.pageY-$.position().top;return a("html").bind("mousemove.jsp",function(a){r(a.pageY-c,!1)}).bind("mouseup.jsp mouseleave.jsp",q),!1}),h())}function h(){fb.height(hb+"px"),ab=0,gb=P.verticalGutter+fb.outerWidth(),Q.width(R-gb-tb);try{0===eb.position().left&&Q.css("margin-left",gb+"px")}catch(a){}}function i(){Z&&(T.append(a('<div class="jspHorizontalBar" />').append(a('<div class="jspCap jspCapLeft" />'),a('<div class="jspTrack" />').append(a('<div class="jspDrag" />').append(a('<div class="jspDragLeft" />'),a('<div class="jspDragRight" />'))),a('<div class="jspCap jspCapRight" />'))),lb=T.find(">.jspHorizontalBar"),mb=lb.find(">.jspTrack"),bb=mb.find(">.jspDrag"),P.showArrows&&(pb=a('<a class="jspArrow jspArrowLeft" />').bind("mousedown.jsp",m(-1,0)).bind("click.jsp",G),qb=a('<a class="jspArrow jspArrowRight" />').bind("mousedown.jsp",m(1,0)).bind("click.jsp",G),P.arrowScrollOnHover&&(pb.bind("mouseover.jsp",m(-1,0,pb)),qb.bind("mouseover.jsp",m(1,0,qb))),l(mb,P.horizontalArrowPositions,pb,qb)),bb.hover(function(){bb.addClass("jspHover")},function(){bb.removeClass("jspHover")}).bind("mousedown.jsp",function(b){a("html").bind("dragstart.jsp selectstart.jsp",G),bb.addClass("jspActive");var c=b.pageX-bb.position().left;return a("html").bind("mousemove.jsp",function(a){t(a.pageX-c,!1)}).bind("mouseup.jsp mouseleave.jsp",q),!1}),nb=T.innerWidth(),j())}function j(){T.find(">.jspHorizontalBar>.jspCap:visible,>.jspHorizontalBar>.jspArrow").each(function(){nb-=a(this).outerWidth()}),mb.width(nb+"px"),db=0}function k(){if(Z&&Y){var b=mb.outerHeight(),c=fb.outerWidth();hb-=b,a(lb).find(">.jspCap:visible,>.jspArrow").each(function(){nb+=a(this).outerWidth()}),nb-=c,S-=c,R-=b,mb.parent().append(a('<div class="jspCorner" />').css("width",b+"px")),h(),j()}Z&&Q.width(T.outerWidth()-tb+"px"),V=Q.outerHeight(),X=V/S,Z&&(ob=Math.ceil(1/W*nb),ob>P.horizontalDragMaxWidth?ob=P.horizontalDragMaxWidth:ob<P.horizontalDragMinWidth&&(ob=P.horizontalDragMinWidth),bb.width(ob+"px"),cb=nb-ob,u(db)),Y&&(ib=Math.ceil(1/X*hb),ib>P.verticalDragMaxHeight?ib=P.verticalDragMaxHeight:ib<P.verticalDragMinHeight&&(ib=P.verticalDragMinHeight),$.height(ib+"px"),_=hb-ib,s(ab))}function l(a,b,c,d){var e,f="before",g="after";"os"==b&&(b=/Mac/.test(navigator.platform)?"after":"split"),b==f?g=b:b==g&&(f=b,e=c,c=d,d=e),a[f](c)[g](d)}function m(a,b,c){return function(){return n(a,b,this,c),this.blur(),!1}}function n(b,c,d,e){d=a(d).addClass("jspActive");var f,g,h=!0,i=function(){0!==b&&vb.scrollByX(b*P.arrowButtonSpeed),0!==c&&vb.scrollByY(c*P.arrowButtonSpeed),g=setTimeout(i,h?P.initialDelay:P.arrowRepeatFreq),h=!1};i(),f=e?"mouseout.jsp":"mouseup.jsp",e=e||a("html"),e.bind(f,function(){d.removeClass("jspActive"),g&&clearTimeout(g),g=null,e.unbind(f)})}function o(){p(),Y&&fb.bind("mousedown.jsp",function(b){if(b.originalTarget===c||b.originalTarget==b.currentTarget){var d,e=a(this),f=e.offset(),g=b.pageY-f.top-ab,h=!0,i=function(){var a=e.offset(),c=b.pageY-a.top-ib/2,f=S*P.scrollPagePercent,k=_*f/(V-S);if(0>g)ab-k>c?vb.scrollByY(-f):r(c);else{if(!(g>0))return j(),void 0;c>ab+k?vb.scrollByY(f):r(c)}d=setTimeout(i,h?P.initialDelay:P.trackClickRepeatFreq),h=!1},j=function(){d&&clearTimeout(d),d=null,a(document).unbind("mouseup.jsp",j)};return i(),a(document).bind("mouseup.jsp",j),!1}}),Z&&mb.bind("mousedown.jsp",function(b){if(b.originalTarget===c||b.originalTarget==b.currentTarget){var d,e=a(this),f=e.offset(),g=b.pageX-f.left-db,h=!0,i=function(){var a=e.offset(),c=b.pageX-a.left-ob/2,f=R*P.scrollPagePercent,k=cb*f/(U-R);if(0>g)db-k>c?vb.scrollByX(-f):t(c);else{if(!(g>0))return j(),void 0;c>db+k?vb.scrollByX(f):t(c)}d=setTimeout(i,h?P.initialDelay:P.trackClickRepeatFreq),h=!1},j=function(){d&&clearTimeout(d),d=null,a(document).unbind("mouseup.jsp",j)};return i(),a(document).bind("mouseup.jsp",j),!1}})}function p(){mb&&mb.unbind("mousedown.jsp"),fb&&fb.unbind("mousedown.jsp")}function q(){a("html").unbind("dragstart.jsp selectstart.jsp mousemove.jsp mouseup.jsp mouseleave.jsp"),$&&$.removeClass("jspActive"),bb&&bb.removeClass("jspActive")}function r(a,b){Y&&(0>a?a=0:a>_&&(a=_),b===c&&(b=P.animateScroll),b?vb.animate($,"top",a,s):($.css("top",a),s(a)))}function s(a){a===c&&(a=$.position().top),T.scrollTop(0),ab=a;var b=0===ab,e=ab==_,f=a/_,g=-f*(V-S);(wb!=b||yb!=e)&&(wb=b,yb=e,d.trigger("jsp-arrow-change",[wb,yb,xb,zb])),v(b,e),Q.css("top",g),d.trigger("jsp-scroll-y",[-g,b,e]).trigger("scroll")}function t(a,b){Z&&(0>a?a=0:a>cb&&(a=cb),b===c&&(b=P.animateScroll),b?vb.animate(bb,"left",a,u):(bb.css("left",a),u(a)))}function u(a){a===c&&(a=bb.position().left),T.scrollTop(0),db=a;var b=0===db,e=db==cb,f=a/cb,g=-f*(U-R);(xb!=b||zb!=e)&&(xb=b,zb=e,d.trigger("jsp-arrow-change",[wb,yb,xb,zb])),w(b,e),Q.css("left",g),d.trigger("jsp-scroll-x",[-g,b,e]).trigger("scroll")}function v(a,b){P.showArrows&&(jb[a?"addClass":"removeClass"]("jspDisabled"),kb[b?"addClass":"removeClass"]("jspDisabled"))}function w(a,b){P.showArrows&&(pb[a?"addClass":"removeClass"]("jspDisabled"),qb[b?"addClass":"removeClass"]("jspDisabled"))}function x(a,b){var c=a/(V-S);r(c*_,b)}function y(a,b){var c=a/(U-R);t(c*cb,b)}function z(b,c,d){var e,f,g,h,i,j,k,l,m,n=0,o=0;try{e=a(b)}catch(p){return}for(f=e.outerHeight(),g=e.outerWidth(),T.scrollTop(0),T.scrollLeft(0);!e.is(".jspPane");)if(n+=e.position().top,o+=e.position().left,e=e.offsetParent(),/^body|html$/i.test(e[0].nodeName))return;h=B(),j=h+S,h>n||c?l=n-P.verticalGutter:n+f>j&&(l=n-S+f+P.verticalGutter),isNaN(l)||x(l,d),i=A(),k=i+R,i>o||c?m=o-P.horizontalGutter:o+g>k&&(m=o-R+g+P.horizontalGutter),isNaN(m)||y(m,d)}function A(){return-Q.position().left}function B(){return-Q.position().top}function C(){var a=V-S;return a>20&&a-B()<10}function D(){var a=U-R;return a>20&&a-A()<10}function E(){T.unbind(Bb).bind(Bb,function(a,b,c,d){var e=db,f=ab;return vb.scrollBy(c*P.mouseWheelSpeed,-d*P.mouseWheelSpeed,!1),e==db&&f==ab})}function F(){T.unbind(Bb)}function G(){return!1}function H(){Q.find(":input,a").unbind("focus.jsp").bind("focus.jsp",function(a){z(a.target,!1)})}function I(){Q.find(":input,a").unbind("focus.jsp")}function J(){function b(){var a=db,b=ab;switch(c){case 40:vb.scrollByY(P.keyboardSpeed,!1);break;case 38:vb.scrollByY(-P.keyboardSpeed,!1);break;case 34:case 32:vb.scrollByY(S*P.scrollPagePercent,!1);break;case 33:vb.scrollByY(-S*P.scrollPagePercent,!1);break;case 39:vb.scrollByX(P.keyboardSpeed,!1);break;case 37:vb.scrollByX(-P.keyboardSpeed,!1)}return e=a!=db||b!=ab}var c,e,f=[];Z&&f.push(lb[0]),Y&&f.push(eb[0]),Q.focus(function(){d.focus()}),d.attr("tabindex",0).unbind("keydown.jsp keypress.jsp").bind("keydown.jsp",function(d){if(d.target===this||f.length&&a(d.target).closest(f).length){var g=db,h=ab;switch(d.keyCode){case 40:case 38:case 34:case 32:case 33:case 39:case 37:c=d.keyCode,b();break;case 35:x(V-S),c=null;break;case 36:x(0),c=null}return e=d.keyCode==c&&g!=db||h!=ab,!e}}).bind("keypress.jsp",function(a){return a.keyCode==c&&b(),!e}),P.hideFocus?(d.css("outline","none"),"hideFocus"in T[0]&&d.attr("hideFocus",!0)):(d.css("outline",""),"hideFocus"in T[0]&&d.attr("hideFocus",!1))}function K(){d.attr("tabindex","-1").removeAttr("tabindex").unbind("keydown.jsp keypress.jsp")}function L(){if(location.hash&&location.hash.length>1){var b,c,d=escape(location.hash.substr(1));try{b=a("#"+d+', a[name="'+d+'"]')}catch(e){return}b.length&&Q.find(d)&&(0===T.scrollTop()?c=setInterval(function(){T.scrollTop()>0&&(z(b,!0),a(document).scrollTop(T.position().top),clearInterval(c))},50):(z(b,!0),a(document).scrollTop(T.position().top)))}}function M(){a(document.body).data("jspHijack")||(a(document.body).data("jspHijack",!0),a(document.body).delegate("a[href*=#]","click",function(c){var d,e,f,g,h,i,j=this.href.substr(0,this.href.indexOf("#")),k=location.href;if(-1!==location.href.indexOf("#")&&(k=location.href.substr(0,location.href.indexOf("#"))),j===k){d=escape(this.href.substr(this.href.indexOf("#")+1));try{e=a("#"+d+', a[name="'+d+'"]')}catch(l){return}e.length&&(f=e.closest(".jspScrollable"),g=f.data("jsp"),g.scrollToElement(e,!0),f[0].scrollIntoView&&(h=a(b).scrollTop(),i=e.offset().top,(h>i||i>h+a(b).height())&&f[0].scrollIntoView()),c.preventDefault())}}))}function N(){var a,b,c,d,e,f=!1;T.unbind("touchstart.jsp touchmove.jsp touchend.jsp click.jsp-touchclick").bind("touchstart.jsp",function(g){var h=g.originalEvent.touches[0];a=A(),b=B(),c=h.pageX,d=h.pageY,e=!1,f=!0}).bind("touchmove.jsp",function(g){if(f){var h=g.originalEvent.touches[0],i=db,j=ab;return vb.scrollTo(a+c-h.pageX,b+d-h.pageY),e=e||Math.abs(c-h.pageX)>5||Math.abs(d-h.pageY)>5,i==db&&j==ab}}).bind("touchend.jsp",function(){f=!1}).bind("click.jsp-touchclick",function(){return e?(e=!1,!1):void 0})}function O(){var a=B(),b=A();d.removeClass("jspScrollable").unbind(".jsp"),d.replaceWith(Ab.append(Q.children())),Ab.scrollTop(a),Ab.scrollLeft(b),rb&&clearInterval(rb)}var P,Q,R,S,T,U,V,W,X,Y,Z,$,_,ab,bb,cb,db,eb,fb,gb,hb,ib,jb,kb,lb,mb,nb,ob,pb,qb,rb,sb,tb,ub,vb=this,wb=!0,xb=!0,yb=!1,zb=!1,Ab=d.clone(!1,!1).empty(),Bb=a.fn.mwheelIntent?"mwheelIntent.jsp":"mousewheel.jsp";"border-box"===d.css("box-sizing")?(sb=0,tb=0):(sb=d.css("paddingTop")+" "+d.css("paddingRight")+" "+d.css("paddingBottom")+" "+d.css("paddingLeft"),tb=(parseInt(d.css("paddingLeft"),10)||0)+(parseInt(d.css("paddingRight"),10)||0)),a.extend(vb,{reinitialise:function(b){b=a.extend({},P,b),f(b)},scrollToElement:function(a,b,c){z(a,b,c)},scrollTo:function(a,b,c){y(a,c),x(b,c)},scrollToX:function(a,b){y(a,b)},scrollToY:function(a,b){x(a,b)},scrollToPercentX:function(a,b){y(a*(U-R),b)},scrollToPercentY:function(a,b){x(a*(V-S),b)},scrollBy:function(a,b,c){vb.scrollByX(a,c),vb.scrollByY(b,c)},scrollByX:function(a,b){var c=A()+Math[0>a?"floor":"ceil"](a),d=c/(U-R);t(d*cb,b)},scrollByY:function(a,b){var c=B()+Math[0>a?"floor":"ceil"](a),d=c/(V-S);r(d*_,b)},positionDragX:function(a,b){t(a,b)},positionDragY:function(a,b){r(a,b)},animate:function(a,b,c,d){var e={};e[b]=c,a.animate(e,{duration:P.animateDuration,easing:P.animateEase,queue:!1,step:d})},getContentPositionX:function(){return A()},getContentPositionY:function(){return B()},getContentWidth:function(){return U},getContentHeight:function(){return V},getPercentScrolledX:function(){return A()/(U-R)},getPercentScrolledY:function(){return B()/(V-S)},getIsScrollableH:function(){return Z},getIsScrollableV:function(){return Y},getContentPane:function(){return Q},scrollToBottom:function(a){r(_,a)},hijackInternalLinks:a.noop,destroy:function(){O()}}),f(e)}return d=a.extend({},a.fn.jScrollPane.defaults,d),a.each(["arrowButtonSpeed","trackClickSpeed","keyboardSpeed"],function(){d[this]=d[this]||d.speed}),this.each(function(){var b=a(this),c=b.data("jsp");c?c.reinitialise(d):(a("script",b).filter('[type="text/javascript"],:not([type])').remove(),c=new e(b,d),b.data("jsp",c))})},a.fn.jScrollPane.defaults={showArrows:!1,maintainPosition:!0,stickToBottom:!1,stickToRight:!1,clickOnTrack:!0,autoReinitialise:!1,autoReinitialiseDelay:500,verticalDragMinHeight:0,verticalDragMaxHeight:99999,horizontalDragMinWidth:0,horizontalDragMaxWidth:99999,contentWidth:c,animateScroll:!1,animateDuration:300,animateEase:"linear",hijackInternalLinks:!1,verticalGutter:4,horizontalGutter:4,mouseWheelSpeed:3,arrowButtonSpeed:0,arrowRepeatFreq:50,arrowScrollOnHover:!1,trackClickSpeed:0,trackClickRepeatFreq:70,verticalArrowPositions:"split",horizontalArrowPositions:"split",enableKeyboardNavigation:!0,hideFocus:!1,keyboardSpeed:0,initialDelay:300,speed:30,scrollPagePercent:.8}}(jQuery,this);

/*! Copyright (c) 2013 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.1.3
 *
 * Requires: 1.2.2+
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'];
    var toBind = 'onwheel' in document || document.documentMode >= 9 ? ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'];
    var lowestDelta, lowestDeltaXY;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    $.event.special.mousewheel = {
        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
        },

        unmousewheel: function(fn) {
            return this.unbind("mousewheel", fn);
        }
    });


    function handler(event) {
        var orgEvent = event || window.event,
            args = [].slice.call(arguments, 1),
            delta = 0,
            deltaX = 0,
            deltaY = 0,
            absDelta = 0,
            absDeltaXY = 0,
            fn;
        event = $.event.fix(orgEvent);
        event.type = "mousewheel";

        // Old school scrollwheel delta
        if ( orgEvent.wheelDelta ) { delta = orgEvent.wheelDelta; }
        if ( orgEvent.detail )     { delta = orgEvent.detail * -1; }

        // New school wheel delta (wheel event)
        if ( orgEvent.deltaY ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( orgEvent.deltaX ) {
            deltaX = orgEvent.deltaX;
            delta  = deltaX * -1;
        }

        // Webkit
        if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY; }
        if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = orgEvent.wheelDeltaX * -1; }

        // Look for lowest delta to normalize the delta values
        absDelta = Math.abs(delta);
        if ( !lowestDelta || absDelta < lowestDelta ) { lowestDelta = absDelta; }
        absDeltaXY = Math.max(Math.abs(deltaY), Math.abs(deltaX));
        if ( !lowestDeltaXY || absDeltaXY < lowestDeltaXY ) { lowestDeltaXY = absDeltaXY; }

        // Get a whole value for the deltas
        fn = delta > 0 ? 'floor' : 'ceil';
        delta  = Math[fn](delta / lowestDelta);
        deltaX = Math[fn](deltaX / lowestDeltaXY);
        deltaY = Math[fn](deltaY / lowestDeltaXY);

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

}));

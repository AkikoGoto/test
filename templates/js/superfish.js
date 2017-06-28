/*
 * Superfish v1.4.1 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 *
 *prototype.js‚Æ‚Æ‚à‚ÉŽg—p‚·‚é‚½‚ß‚ÉA$‚ðjQuery‚ÉC³
 */

(function(jQuery){
	jQuery.superfish = {};
	jQuery.superfish.o = [];
	jQuery.superfish.op = {};
	jQuery.superfish.defaults = {
		hoverClass	: 'sfHover',
		pathClass	: 'overideThisToUse',
		delay		: 800,
		animation	: {opacity:'show'},
		speed		: 'normal',
		oldJquery	: false, /* set to true if using jQuery version below 1.2 */
		disableHI	: false, /* set to true to disable hoverIntent usage */
		// callback functions:
		onInit		: function(){},
		onBeforeShow: function(){},
		onShow		: function(){}, /* note this name changed ('onshow' to 'onShow') from version 1.4 onward */
		onHide		: function(){}
	};
	jQuery.fn.superfish = function(op){
		var bcClass = 'sfbreadcrumb',
			over = function(){
				var jQueryjQuery = jQuery(this), menu = getMenu(jQueryjQuery);
				getOpts(menu,true);
				clearTimeout(menu.sfTimer);
				jQueryjQuery.showSuperfishUl().siblings().hideSuperfishUl();
			},
			out = function(){
				var jQueryjQuery = jQuery(this), menu = getMenu(jQueryjQuery);
				var o = getOpts(menu,true);
				clearTimeout(menu.sfTimer);
				if ( !jQueryjQuery.is('.'+bcClass) ) {
					menu.sfTimer=setTimeout(function(){
						jQueryjQuery.hideSuperfishUl();
						if (o.jQuerypath.length){over.call(o.jQuerypath);}
					},o.delay);
				}		
			},
			getMenu = function(jQueryel){ return jQueryel.parents('ul.superfish:first')[0]; },
			getOpts = function(el,menuFound){ el = menuFound ? el : getMenu(el); return jQuery.superfish.op = jQuery.superfish.o[el.serial]; },
			hasUl = function(){ return jQuery.superfish.op.oldJquery ? 'li[ul]' : 'li:has(ul)'; };

		return this.each(function() {
			var s = this.serial = jQuery.superfish.o.length;
			var o = jQuery.extend({},jQuery.superfish.defaults,op);
			o.jQuerypath = jQuery('li.'+o.pathClass,this).each(function(){
				jQuery(this).addClass(o.hoverClass+' '+bcClass)
					.filter(hasUl()).removeClass(o.pathClass);
			});
			jQuery.superfish.o[s] = jQuery.superfish.op = o;
			
			jQuery(hasUl(),this)[(jQuery.fn.hoverIntent && !o.disableHI) ? 'hoverIntent' : 'hover'](over,out)
			.not('.'+bcClass)
				.hideSuperfishUl();
			
			var jQuerya = jQuery('a',this);
			jQuerya.each(function(i){
				var jQueryli = jQuerya.eq(i).parents('li');
				jQuerya.eq(i).focus(function(){over.call(jQueryli);}).blur(function(){out.call(jQueryli);});
			});
			
			o.onInit.call(this);
			
		}).addClass('superfish');
	};
	
	jQuery.fn.extend({
		hideSuperfishUl : function(){
			var o = jQuery.superfish.op,
				jQueryul = jQuery('li.'+o.hoverClass,this).add(this).removeClass(o.hoverClass)
					.find('>ul').hide().css('visibility','hidden');
			o.onHide.call(jQueryul);
			
			return this;
		},
		showSuperfishUl : function(){
			var o = jQuery.superfish.op,
				jQueryul = this.addClass(o.hoverClass)
					.find('>ul:hidden').css('visibility','visible');
			o.onBeforeShow.call(jQueryul);
			jQueryul.animate(o.animation,o.speed,function(){ o.onShow.call(this); });
			return this;
		}
	});
	
	jQuery(window).unload(function(){
		jQuery('ul.superfish').each(function(){
			jQuery('li',this).unbind('mouseover','mouseout','mouseenter','mouseleave');
		});
	});
})(jQuery);
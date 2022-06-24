( function ( $ ) {
	// ready event
	function tophive_cn_set_cookie(cookieName, cookieValue, cookie_expiry) {
		var today = new Date();
		var expire = new Date();

		switch( cookie_expiry ){
			case 'hour':
				expire.setTime(today.getTime() + 60*60*1000);
				break;
			case 'day':
				expire.setTime(today.getTime() + 60*60*24*1000);
				break;
			case 'week':
				expire.setTime(today.getTime() + 60*60*24*7*1000);
				break;
			case 'month':
				expire.setTime(today.getTime() + 60*60*24*30*1000);
				break;
			case '3months':
				expire.setTime(today.getTime() + 60*60*24*90*1000);
				break;
			case '6months':
				expire.setTime(today.getTime() + 60*60*24*180*1000);
				break;
			case 'year':
				expire.setTime(today.getTime() + 60*60*24*365*1000);
				break;
			case 'infinity':
				expire.setTime(today.getTime() + 60*60*24*365*50*1000);
				break;
			default:
				expire.setTime(today.getTime() + 60*60*1000);
		}
		
		document.cookie = cookieName+"="+escape(cookieValue)+ ";expires="+expire.toGMTString()+"; path=/";
	}
	function tophive_cn_get_cookie(cookieName) {
		var theCookie=" "+document.cookie;
		var ind=theCookie.indexOf(" "+cookieName+"=");
		if (ind==-1) ind=theCookie.indexOf(";"+cookieName+"=");
		if (ind==-1 || cookieName=="") return "";
		var ind1=theCookie.indexOf(";",ind+1);
		if (ind1==-1) ind1=theCookie.length; 
		// Returns true if the versions match
		return 'true' == unescape(theCookie.substring(ind+cookieName.length+2,ind1));
	}
	function tophive_cn_delete_cookie(cookieName) {
		document.cookie = cookieName + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
	}
	function tophive_accept_cookie( cookie_value ) {
		tophive_cn_set_cookie('tophive_cn_accept', cookie_value, TophiveCore_JS.cn_cookie_expiry);
		$( 'html' ).addClass( 'no-cookie-bar' );
		$( '#tophive_cookie_notice' ).fadeOut();
	}
	
	$(document).ready(function($){
		$(document).on('click', '#tophive-accept-cookie', function( e ){
			e.preventDefault();
			var cookie_value  = $(this).data( 'cookie-set' );
			cookie_value = cookie_value === 'accept' ? 'true' : 'false';
			tophive_accept_cookie( cookie_value );
		});

		if( !tophive_cn_get_cookie( 'tophive_cn_accept' ) ){
			$('body').addClass('tophive-has-cookie-bar');
			var notice_box = $( '#tophive_cookie_notice' );
			if( notice_box.is( '.cn-position-top' ) || notice_box.is( '.cn-position-bottom' ) ){
				var out_height = notice_box.outerHeight();
				if( notice_box.is( '.cn-position-top' ) ){
					if( $('body').is( '.admin-bar' ) ){
						var top_offset = out_height - 32;
					}else{
						top_offset = out_height;
					}
					
					$('body').css('margin-top', top_offset);
				}else if( notice_box.is( '.cn-position-bottom' ) ){
					$('body').css('padding-bottom', out_height);
				}
			}
		}
	});
} )( jQuery );
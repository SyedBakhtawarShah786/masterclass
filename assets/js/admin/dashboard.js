jQuery( document ).ready( function( $ ){

	var primaryprogress = 5;

    $(document).on( 'click', '.start-import-popup', function( e ){
        e.preventDefault();
        var thumb = $(this).data('thumb');
        var img = '<img src="' + thumb + '" />';
        $('.tophive-demo-importer-wrapper .tophive-demo-importer-popup-thumb').html(img);
        $('.tophive-demo-importer-wrapper').addClass('active');
        var item = $(this).data('slug');
        var selected = $(this).data('id');
        $('.tophive-demo-importer-wrapper a').attr('data-slug', item);
        $.ajax({
        	url: TophiveAdminAjax.ajaxurl,
			type: 'POST',
			data: {
				'action' : 'process_required_plugins',
				'item' : item,
				'selected': selected,
			},
			beforeSend: function(){
				var loader = "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='margin: auto; background: none; display: block; shape-rendering: auto;' width='137px' height='137px' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid'><path d='M35 50A15 15 0 0 0 65 50A15 16.3 0 0 1 35 50' fill='#292664' stroke='none' transform='rotate(177.696 50 50.65)'><animateTransform attributeName='transform' type='rotate' dur='0.5025125628140703s' repeatCount='indefinite' keyTimes='0;1' values='0 50 50.65;360 50 50.65'></animateTransform></path></svg>";
				$('.tophive-demo-importer-popup-content').html(loader);
			},
			success : function( res ){
				$('.tophive-demo-importer-popup-content').html(res);
			},
			error: function(xhr, ajaxOptions, thrownError){
				console.log(xhr.responseText);
	        }
        });
    });
    $(document).on('click', '.tophive-demo-plugin-install', function( e ){
    	e.preventDefault();
    	var slug = $(this).data('slug');
        var selected = $(this).data('id');

        var pluginsList = [];
        var plugins = $('ul.popup-plugins-list>li');
        $.each( plugins , function( i, el ){
        	pluginsList.push( $(el).data('slug') );
        });
    	var currentplugin = pluginsList[0];
        var data = {
			'action' : 'demos_plugin_status_check',
			'slug' : slug,
			'selected': selected,
			'plugins': pluginsList
		}
		PluginInstallationFunc( data );
    	$(this).closest('.import-inner-footer').addClass('loading');
    });
    function PluginInstallationFunc( data ){
		$.ajax({
        	url: TophiveAdminAjax.ajaxurl,
			type: 'POST',
			data: data,
			
        }).done(function (res){
        	console.log(res);
			if(  '' !== res.status && 'uninstalled' == res.status  ){
				var newData = {
					'action' : 'demos_plugin_install',
					'plugin' : res.plugin
				}
				$('.popup-plugins-list li.' + res.plugin + ' span:last-child').addClass('warning').html(res.text);
				PluginInstallationFunc( newData );
			}
			else if( '' !== res.status && 'installed' == res.status ){
				var newData = {
					'action' : 'demos_plugin_activate',
					'plugin' : res.plugin
				}
				$('.popup-plugins-list li.' + res.plugin + ' span:last-child').html(res.text);
				PluginInstallationFunc( newData );
			}else if( '' !== res.status && 'active' == res.status ){
				if( res.next_plugin !== 'done' ){
					var newData = {
						'action' : 'demos_plugin_check_next',
						'plugin' : res.next_plugin
					}
					$('.popup-plugins-list li.' + res.plugin + ' span:last-child').removeClass('warning').addClass('success').html(res.text);
					PluginInstallationFunc( newData );
				}else{
					$('.popup-plugins-list li.' + res.plugin + ' span:last-child').removeClass('warning').addClass('success').html(res.text);
					var newData = {
						'action' : 'plugins_ok_footer'
					}
					PluginInstallationFunc( newData );
				}
			}else if( '' !== res.status && 'next' == res.status ){
				$('.tophive-demo-importer-popup-content .import-inner-footer').html(res.html);
				$('.tophive-demo-importer-popup-content .import-inner-footer').removeClass('loading');
			}
        }).fail( function(error) {
			console.log(error);
        })
    }
    function ImportAjaxCall( data ) {
		$.ajax({
			url: 		 TophiveAdminAjax.ajaxurl,
			type:      'POST',
			data:        data,
			beforeSend:  function() {}
		})
		.done( function( response ) {
			console.log(response);
			if ( 'undefined' !== typeof response.status && 'newAJAX' === response.status ) {
				ImportAjaxCall( data );
			}
			else if ( 'undefined' !== typeof response.status && 'customizerAJAX' === response.status ) {
				primaryprogress = 90;
				var currentprogress = primaryprogress + '%'; 
				$('.tophive-progress-bar span').html(currentprogress);
				$('.tophive-progress-bar').css('width' , currentprogress);
				var newData = {
					'action' : 'tophive_import_customizer_data',
					'security' : TophiveAdminAjax.ajax_nonce
				}
				ImportAjaxCall( newData );
			}
			else if ( 'undefined' !== typeof response.status && 'afterAllImportAJAX' === response.status ) {
				primaryprogress = 100;
				var currentprogress = primaryprogress + '%'; 
				$('.tophive-progress-bar span').html(currentprogress);
				$('.tophive-progress-bar').css('width' , currentprogress);
				var newData = {
					'action' : 'ocdi_after_import_data',
					'security' : TophiveAdminAjax.ajax_nonce
				}
				ImportAjaxCall( newData );
			}
			else if ( 'undefined' !== typeof response.message ) {
				$('.tophive-demo-importer-popup-content').html(response.message);
			}
			else {
				console.log(response);
			}
		})
		.fail( function( error ) {
			console.log( error );
		});
	}
    $(document).on('click', '.start-demo-import',  function(e){
    	e.preventDefault();
    	var _that = $(this);
    	var slug = $(this).data('slug');
        var selected = $(this).data('id');

    	var data = {
			'action' : 'process_demo_import',
			'security' : TophiveAdminAjax.ajax_nonce,
			'slug' : slug,
			'selected': selected
		}
		primaryprogress = 5;
		$('.start-demo-import').parent().find('a').fadeOut(200, function(){
			$('.tophive-progress-bar span').html('5%');
			$('.start-demo-import').parent().find('.tophive-progress').fadeIn(300);
		});
		setInterval( function(){
			if( primaryprogress == 100 ){
				primaryprogress = 100;
			}else if( primaryprogress == 90 ){
				primaryprogress = 90;
			}else if( primaryprogress < 81 && primaryprogress > 77 ){
				primaryprogress = 80;
			}else{
				primaryprogress += 1;
			}
			var currentprogress = primaryprogress + '%'; 
			$('.tophive-progress-bar span').html(currentprogress);
			$('.tophive-progress-bar').css('width' , currentprogress);
		}, 2000);
		ImportAjaxCall( data );
    });
    
    $(document).on('click', '.tophive-demo-importer-popup-content .plugins-ok', function(e){
    	e.preventDefault();
    	var item = $(this).data('slug');
        var selected = $(this).data('id');

        $.ajax({
        	url: TophiveAdminAjax.ajaxurl,
			type: 'POST',
			data: {
				'action' : 'process_demo_import_html',
				'item' : item,
				'selected': selected
			},
			beforeSend: function(){
				var loader = "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='margin: auto; background: none; display: block; shape-rendering: auto;' width='137px' height='137px' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid'><path d='M35 50A15 15 0 0 0 65 50A15 16.3 0 0 1 35 50' fill='#292664' stroke='none' transform='rotate(177.696 50 50.65)'><animateTransform attributeName='transform' type='rotate' dur='0.5025125628140703s' repeatCount='indefinite' keyTimes='0;1' values='0 50 50.65;360 50 50.65'></animateTransform></path></svg>";
				$('.tophive-demo-importer-popup-content').html(loader);
			},
			success : function( res ){
				$('.tophive-demo-importer-popup-content').html(res);
			},
			error: function(xhr, ajaxOptions, thrownError){
				console.log(xhr.responseText);
	        }
        });
    });
    $('input[name="tophive_selected_plugins"]').change(function() {
    	var plugins = [];
    	$.each( $('input[name="tophive_selected_plugins"]:checked'), function(){
    		plugins.push( $(this).val() );
    	});
        if( plugins.length > 0 ) {
			$('.tophive-plugins-action').removeClass('deactive');
        }else{
			$('.tophive-plugins-action').addClass('deactive');
        }        
    });
    var done_icon = '<svg class="tophive-active" width="1.3em" height="1.3em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path></svg>';
					
    function SitePluginsInstaller( data ){
		$.ajax({
        	url: TophiveAdminAjax.ajaxurl,
			type: 'POST',
			data: data,
			
        }).done(function (res){
			if(  '' !== res.status && 'uninstalled' == res.status  ){
				var newData = {
					'action' : 'demos_plugin_install',
					'plugin' : res.plugin
				}
				$('.tophive-plugin-installation tr.' + res.plugin + ' td.plugin-status').removeClass('tophive-uninstalled').addClass('tophive-installed').html(res.text);
				SitePluginsInstaller( newData );
			}
			else if( '' !== res.status && 'installed' == res.status ){
				var newData = {
					'action' : 'demos_plugin_activate',
					'plugin' : res.plugin
				}
				$('.tophive-plugin-installation tr.' + res.plugin + ' td.plugin-status').html(res.text);
				SitePluginsInstaller( newData );
			}else if( '' !== res.status && 'active' == res.status ){
				if( res.next_plugin !== 'done' ){
					var newData = {
						'action' : 'demos_plugin_check_next',
						'plugin' : res.next_plugin
					}
					$('.tophive-plugin-installation tr.' + res.plugin + ' td.plugin-status').removeClass('tophive-installed').addClass('tophive-active').html(res.text);
					$('.tophive-plugin-installation tr.' + res.plugin + ' td.action').html(done_icon);
					SitePluginsInstaller( newData );
				}else{
					$('.tophive-plugin-installation tr.' + res.plugin + ' td.plugin-status').removeClass('tophive-installed').addClass('tophive-active').html(res.text);
					$('.tophive-plugin-installation tr.' + res.plugin + ' td.action').html(done_icon);
					var newData = {
						'action' : 'plugins_done_footer'
					}
					SitePluginsInstaller( newData );
				}
			}else if( '' !== res.status && 'next' == res.status ){
				$('.tophive-plugins-action').removeClass('loading');
				var button = $('.tophive-plugins-action a');
				$('.tophive-plugins-action a').fadeOut(200, function(){
					$('.tophive-plugins-action').html('<span class="confirm">' + res.html + '</span>');
				});
				setTimeout( function(){
					$('.tophive-plugins-action .confirm').fadeOut(200, function(){
						$('.tophive-plugins-action .confirm').html('');
						$('.tophive-plugins-action').addClass('deactive').html(button);
						$('.tophive-plugins-action a').fadeIn(200);
					});
				}, 4000);
			}
        }).fail( function(error) {
			console.log(error);
        })
    }
    $(document).on('click', '.tophive-plugins-import', function( e ){
    	e.preventDefault();
    	var plugins = [];
		$('.tophive-plugins-action').addClass('loading');
    	$.each( $('input[name="tophive_selected_plugins"]:checked'), function(){
    		plugins.push( $(this).val() );
    	});
    	if( plugins.length > 0 ){
    		var data = {
				'action' : 'demos_plugin_status_check',
				'plugins': plugins
			}
			SitePluginsInstaller(data);
    	}
    });
    $(document).on('keyup', 'input[name="tophive-theme-purchase-key"]', function(){
    	var inputValue = $('input[name="tophive-theme-purchase-key"]').val();
    	if( '' !== inputValue ){
    		$('.tophive-activate-theme').addClass('tophive-activate-theme-enabled');
    	}else{
    		$('.tophive-activate-theme').removeClass('tophive-activate-theme-enabled');
    	}
    })
    $(document).on('click', '.tophive-activate-theme-enabled', function( e ){
    	e.preventDefault();
    	var purchaseKey = $('input[name="tophive-theme-purchase-key"]').val();
		$(this).addClass('loading');
		$('.tophive-activate-theme').html(TophiveAdminAjax.activating);
    	$.ajax({
			url: TophiveAdminAjax.ajaxurl,
			type: 'POST',
			data: {
				'action' : 'theme_activation',
				'key' : purchaseKey 
			},
			success : function( res ){
				if( res == 'activated' ){
					$('.tophive-activation-success-message').addClass(res);
				}else{
					$('.tophive-activate-theme').html(TophiveAdminAjax.activation_text);
					$('.tophive-messages').html(res);
				}
			},
			error: function(xhr, ajaxOptions, thrownError){
				console.log(xhr.responseText);
	        }
		})
    });
    $(document).on( 'click', '.end-import-popup', function( e ){
        e.preventDefault();
        $('.tophive-demo-importer-wrapper').removeClass('active');
    } );
    $(document).keyup(function( e ){
    	keyPress(e);
    });
	$('.tabs a').click(function(){

		$('.panel').hide();
		$('.tabs a.active').removeClass('active');
		$(this).addClass('active');

		var panel = $(this).attr('href');
		$(panel).fadeIn(200);

		return false;

	}); 

	$('.tabs li:first a').click();
     
    function keyPress (e) {
	    if(e.key === "Escape") {
    	    $('.tophive-demo-importer-wrapper').removeClass('active');
	    }
	}

	$( '.tophive-regenerate-assets' ).on( 'click', function(e){
        e.preventDefault();
        var button =  $( this );
        button.addClass( 'updating-message disabled' );
        $.get( TophiveAdminAjax.regenerate_url, function(){
            button.removeClass('updating-message disabled');
            $('.regeneration-completed').html( TophiveAdminAjax.regenerate_done );
        });
    });
});
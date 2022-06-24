/*
** Tophive Megamenu Powerd by Tophive INC.

*/

jQuery(document).ready(function(jQuery){
	var sidebarWidth = jQuery("#customize-controls").width();

	jQuery('.tophive--mm-builder-inner').css({ 'margin-left': sidebarWidth });

	jQuery(document).on('click', '.tophive-mm-section-open', function(e){
		jQuery('.customize-control-nav_menu_item.menu-item.menu-item-depth-0 .menu-item-settings').prepend('<span class="tophive-mega-menu-pointer">Mega Menu</span>');
		jQuery('.customize-control-nav_menu_item.menu-item.menu-item-depth-0').addClass('tophive-megamenu-active');
		jQuery(this).attr('value', 'Deactivate Megamenu');
	});

	jQuery(document).on('click', '.tophive-megamenu-active', function(e){
		jQuery(document).find('.tophive--customize-mm-builder').addClass('tophive-mm-open');
	});

	jQuery(document).on('click', '#menu-to-edit .customize-section-back', function(){
		jQuery('.tophive--customize-mm-builder').removeClass('tophive-mm-open');
	});

	jQuery(document).on('click', '.tophive-layout-selector a', function(e){
		e.preventDefault();
		jQuery(this).parent().find('.tophive-mm-layout-examples').toggleClass('show');
	});

	jQuery(document).on('click', '.tophive-mm-layout-examples span', function(){
		var cols = jQuery(this).data('col');
		var html = includeColumns( cols );
		jQuery('.tophive-layout-selector').html(html);
	});

	var includeColumns = function( cols, selector ){
		var html = availableWidgets();
		html += '<div class="tophive-mm-builder-layout-cols">';
		var i;
		for ( i = 0; i < cols; i++ ) {
			html += '<div class="tophive-col-mm">'+ columnsIcons() +'</div>';
		}
		html += '</div>';
		return html;
	}
	var columnsIcons = function(){
		var html = '';
		html += '<div class="tophive-column-control">';
		html += '<span class="tophive-column-control-delete"><img src="'+ directory_uri.template_directory_uri +'/inc/customizer/megamenu/images/delete.svg" alt="col-dlt" /></span>';
		html += '<span class="tophive-column-control-copy"><img src="'+ directory_uri.template_directory_uri +'/inc/customizer/megamenu/images/copy.svg" alt="col-copy" /></span>';
		html += '</div>';
		html += '<div class="tophive-column-add-widget"><img src="'+ directory_uri.template_directory_uri +'/inc/customizer/megamenu/images/plus.svg" alt="col-copy" /></div>';

		return html;
	}

	var availableWidgets = function(){
		var html = '';
		html += '<div class="tophive-available-widgets" id="tophive-available-widgets">';
		html += '<div class="tophive-mm-widget" data-widid="1" data-widname="Links"><img src="'+ directory_uri.template_directory_uri +'/inc/customizer/megamenu/images/link.png" />Link</div>';
		html += '<div class="tophive-mm-widget" data-widid="2" data-widname="Paragraph"><img src="'+ directory_uri.template_directory_uri +'/inc/customizer/megamenu/images/paragraph.png" />Paragraph</div>';
		html += '<div class="drag"></div>';
		html += '</div>';
		return html;
	}

	var widgetContainer = jQuery('#tophive-available-widgets'),
		widgetDropzone = jQuery('.tophive-column-add-widget');

	jQuery(widgetContainer).find('.tophive-mm-widget').draggable();
});

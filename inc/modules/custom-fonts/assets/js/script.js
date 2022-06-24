jQuery(document).ready( function( $ ) {

    var TophiveFontUpload =  {
        setAttachment: function( attachment ){
            this.attachment = attachment;
        },
        addParamsURL: function( url, data ) {
            if ( ! $.isEmptyObject(data) )
            {
                url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(data);
            }
            return url;
        },
        getThumb: function( attachment ){
            var control = this;
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            var t = new Date().getTime();
            if ( typeof this.attachment.sizes !== "undefined" ) {
                if ( typeof this.attachment.sizes.medium !== "undefined" ) {
                    return control.addParamsURL( this.attachment.sizes.medium.url, { t : t } );
                }
            }
            return control.addParamsURL( this.attachment.url, { t : t } );
        },
        getURL: function( attachment ) {
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            var t = new Date().getTime();
            return this.addParamsURL( this.attachment.url, { t : t } );
        },
        getID: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            return this.attachment.id;
        },
        getInputID: function( attachment ){
            $( '.attachment-id', this.preview ).val( );
        },
        setPreview: function( $el ){
            this.preview = $el;
        },
        insertImage: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }

            var url = this.getURL();
            var id = this.getID();
            var mime = this.attachment.mime;
            $( '.tophive-image-preview', this.preview ).addClass( 'tophive--has-file' ).html(  '<img src="'+url+'" alt="">' );
            $( '.attachment-url', this.preview ).val( this.toRelativeUrl( url ) );
            $( '.attachment-mime', this.preview ).val( mime );
            $( '.attachment-id', this.preview ).val( id ).trigger( 'change' );
            this.preview.addClass( 'attachment-added' );
            this.showChangeBtn();

        },
        toRelativeUrl: function( url ){
            return url;
        },
        showChangeBtn: function(){
            $( '.tophive--add', this.preview ).addClass( 'tophive--hide' );
            $( '.tophive--change', this.preview ).removeClass( 'tophive--hide' );
            $( '.tophive--remove', this.preview ).removeClass( 'tophive--hide' );
        },
        insertVideo: function(attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }

            var url = this.getURL();
            var id = this.getID();
            var mime = this.attachment.mime;
            var html = '<video width="100%" height="" controls><source src="'+url+'" type="'+mime+'">Your browser does not support the video tag.</video>';
            $( '.tophive-image-preview', this.preview ).addClass( 'tophive--has-file' ).html( html );
            $( '.attachment-url', this.preview ).val( this.toRelativeUrl( url ) );
            $( '.attachment-mime', this.preview ).val( mime );
            $( '.attachment-id', this.preview ).val( id ).trigger( 'change' );
            this.preview.addClass( 'attachment-added' );
            this.showChangeBtn();
        },
        insertFile: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            var url = attachment.url;
            var mime = this.attachment.mime;
            var basename = url.replace(/^.*[\\\/]/, '');

            $( '.attachment-url', this.preview ).val( this.toRelativeUrl( url ) );
            $( '.attachment-id', this.preview ).val( this.getID() ).trigger( 'change' );

        },
        remove: function( $el ){
            if ( typeof $el !== "undefined" ) {
                this.preview = $el;
            }

            $( '.attachment-url', this.preview ).val( '' );
            $( '.attachment-id', this.preview ).val( '' ).trigger( 'change' );
        }

    };

    TophiveFontUpload.controlFont = {};

    _.each( Tophive_Fonts_Settings, function( info, ext ){
        TophiveFontUpload.controlFont[ info['mime'] ] = wp.media({
            library: {
                type: info['mime']
            },
            title: info['modal'],
            button: {
               // text: 'Button'
            },
            multiple: false
        });

        TophiveFontUpload.controlFont[ info['mime'] ].on('select', function () {
            var attachment = TophiveFontUpload.controlFont[ info['mime'] ].state().get('selection').first().toJSON();
            TophiveFontUpload.insertFile( attachment );
        });
    } );


    $( document.body ).on( 'click',  '.font-upload-btn', function( e ) {
        e.preventDefault();
        var p = $( this ).closest('.font-file-field');
        var mime = $( this ).attr( 'data-mime' ) || '';
        if ( mime  ) {
            TophiveFontUpload.setPreview(p);
            TophiveFontUpload.controlFont[mime].open();
        }
    } );

    // Add more variation
    $( document.body ).on( 'click', '.font-add-variation', function( e ){
        e.preventDefault();
        var p  = $( this ).closest('.inside');
        var field = $( '.font-file-group', p ).eq( 0 ).clone();
        field.find( '.font-file-input input' ).val( '' ); // reset var
        field.find( 'select option' ).removeAttr( 'selected' ); // reset var
        field.find( '.preview-text' ).removeAttr( 'style' ); // reset var
        field.addClass( 'close' );
        $( '.list-fonts', p ).append( field );
    } );

    // toggle edit
    $( document.body ).on( 'click', '.font-edit, .font-close', function( e ){
        e.preventDefault();
        var p  = $( this ).closest('.font-file-group');
        p.toggleClass( 'close' );
    } );

    // remove
    $( document.body ).on( 'click', '.font-remove', function( e ){
        e.preventDefault();

        var gp  = $( this ).closest('.list-fonts');
        var p  = $( this ).closest('.font-file-group');
        if ( $( '.font-file-group', gp ).length > 1 ) {
            p.remove();
        } else {
            p.find( '.font-file-input input' ).val( '' ); // reset var
            p.find( 'select option' ).removeAttr( 'selected' ); // reset var
            p.find( '.preview-text' ).removeAttr( 'style' ); // reset var
        }

    } );



} );
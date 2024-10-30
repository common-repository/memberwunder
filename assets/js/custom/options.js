(function($, document) {
    $(document).ready(function() {
      /* INIT TABS FOR DASHBOARD OPTIONS */
      var hash = window.location.hash == "" ? sessionStorage.getItem("memberwunder_tab") : window.location.hash.slice(1);
      $( "#twm-tabs" ).tabs({
        active: hash == null ? '' : $('#twm-tabs ul li a[href="#' + hash + '"]').parent().index(),
        activate: function(event, ui) {   
                hash = ui.newPanel[0].id;
                if( window.sessionStorage )
                  sessionStorage.setItem( "memberwunder_tab", hash );
                window.location.hash = hash;

                $('html, body').animate({scrollTop: '0px'}, 400);
            }
      });
      /* END INIT TABS FOR DASHBOARD OPTIONS */

      /* FOR PRO VERSION */
      $( '.for_memberwunder_pro' ).on('click', function() {
          return false;
      });
      $( '.for_memberwunder_pro input, .for_memberwunder_pro textarea' ).attr( 'readonly', true );
      $( '.for_memberwunder_pro:not(input)' ).each(function(){
        $(this).tooltipster({
            content: memberwunder.general.pro_only,
            side: 'right'
        });
      });
      /* END FOR PRO VERSION */

      /* INIT CSS EDITOR */
      var textarea = $('textarea[name="twmembership[custom_css]"]');
      if( textarea.length )
      {
        textarea.after( "<div id='custom_css_block'></div>" );
        textarea.hide();
        
        var editor = ace.edit("custom_css_block");

        editor.getSession().setMode( "ace/mode/css" );
        editor.setTheme( "ace/theme/chrome" );
        editor.getSession().setValue( textarea.val() );
        editor.getSession().on( 'change', function(){
          textarea.val( editor.getSession().getValue() );
        });
      }
      /* END INIT CSS EDITOR */

      /* COLOR ACCORDION */
      $( '#twm-tabs  .form-table .card .twm-custom-theme-color [data-icon]' ).on( 'click', function( event ){
        event.preventDefault();
        $(this).closest('.twm-custom-theme-color').toggleClass( 'twm-opened-style-helps' );
      });
      /* END COLOR ACCORDION */

      /* TABS ON MOBILE DEVICES */
      $(window).on( 'resize', function(){
        if( $( '#twm-tabs select.twn-mobile-tabs' ).length || $(document).width() > 1425 || !$( '#twm-tabs' ).length )
          return;
        
        $( "<select class='twn-mobile-tabs'/>" ).insertBefore( '#twm-tabs ul[role="tablist"]' );

        $( '#twm-tabs ul[role="tablist"] a' ).each(function() {
         $("<option />", {
             "value"   : $(this).attr("href"),
             "text"    : $(this).text()
         }).appendTo("#twm-tabs select.twn-mobile-tabs");
        });

        $( '#twm-tabs select.twn-mobile-tabs option[value="#' + hash + '"]' ).prop( 'selected', 'selected' );        

        $( '#twm-tabs select.twn-mobile-tabs' ).on( 'change', function(){
          $('#twm-tabs').tabs( 'option', 'active', $(this).find("option:selected").index() );
        });
      });
      $(window).trigger('resize');
      /* TABS ON MOBILE DEVICES */

      /* FULLLINK WIDGET */
      $( '#twm-tabs .twn-tab-content-fulllink .twn-tab-content-fulllink-value' ).on( 'click', function( event ){
        event.preventDefault();

        var win = window.open( $(this).text().replace( /\s/g, '' ) + $(this).closest('.twn-tab-content-fulllink').find('input').val(), '_blank' );
        if( win )
          win.focus();
      });
      /* END FULLLINK WIDGET */

      /* ADDED DEPENDET TO OPTION FIELDS */
      $(this).twmDependetLogic();

      $( '#twm-tabs form input, #twm-tabs form select' ).on( 'change', function(){
        $(this).twmDependetLogic();
      });
      /* END ADDED DEPENDET TO OPTION FIELDS */

      /* EXPORT HANDLER */
      $( '#twm-tabs #import tr:not(.for_memberwunder_pro) button[name="import-submit"]' ).on( 'click', function(){
        var wrapper       =   $(this).closest('.import-block'),
            file_input    =   wrapper.find('input[type="file"]').first(),
            file          =   file_input[0].files,
            data          =   new FormData(),
            button        =   $(this),
            button_text   =   $(this).find('span').first(),
            button_label  =   button_text.text();
        
        data.append( file_input.attr( 'name' ), file[0] ); 

        $.ajax({
          url: wrapper.data( 'url' ),
          type: 'POST',
          data: data,
          cache: false,
          timeout: 0,
          processData: false, // Don't process the files
          contentType: false, // Set content type to false as jQuery will tell the server its a query string request
          success: function( data ) { 
            data = JSON.parse( data );

            button_text.text( button_label );
            button.find('img').hide();

            file_input.prop( "disabled", false );
            button.prop( "disabled", false );
            $( '#twm-tabs p.submit input' ).prop( "disabled", false );

            wrapper.after( '<div class="' + data.status + ' notice fade"><p><strong>' + data.message + '</strong></p></div>' );
          },
          beforeSend: function(){
            wrapper.closest( 'td' ).find( '.notice' ).remove();

            file_input.prop( "disabled", true );
            button.prop( "disabled", true );
            $( '#twm-tabs p.submit input' ).prop( "disabled", true );

            button_text.text( button.data( 'start-loading' ) );
            button.find('img').show();
          }
        });
      });
      /* END EXPORT HANDLER */

      /* IMPORT BUTTON */
      var import_parent = $( '#wpbody-content .wrap a.page-title-action' );
      if( import_parent.length )
        import_parent.after( '<a href="' + memberwunder.import.link + '" class="page-title-action">' + memberwunder.import.label + '</a>' );
      /* END IMPORT BUTTON */
    });
}(jQuery, document));

(function($) {
  /* ADDED DEPENDET TO OPTION FIELDS */
  $.fn.twmDependetLogic = function() {
    $( '#twm-tabs form [data-dependet-field]' ).each(function(){
        var primary_field   = $(this).closest( 'form' ).find( '[name="twmembership[' + $(this).data( 'dependet-field' ) + ']"]').last(),
            dependet_value  = $(this).data( 'dependet-value' ),
            wrapper         = $(this).closest('tr'),
            primary_value   = primary_field.val();

        if( primary_field.is(':checkbox') )
          primary_value = primary_field.is(':checked') ? primary_field.val() : $(this).closest( 'form' ).find( '[name="twmembership[' + $(this).data( 'dependet-field' ) + ']"]').first().val();

        wrapper.hide();
        if( dependet_value == primary_value )
          wrapper.show();
      });
  }
  /* END ADDED DEPENDET TO OPTION FIELDS */
})(jQuery);
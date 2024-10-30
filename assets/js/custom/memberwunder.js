(function($, document) {
    $(document).ready(function() {
      var info_blocks = $( '.twm_info_block' );
      if( info_blocks.length )
        info_blocks.each(function(){
          var block = $(this);
          $.ajax({
            url: block.data( 'url' ),
            type: 'GET',
            cache: false,
            timeout: 0,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function( data ) { 
              block.append( data );
            },
          });
        });

      var notices = $( '.memberwunder-notice' );
      if( notices.length )
        notices.each(function(){
          $(this).insertAfter( $( '#wpbody .wrap ' + ( $('#wpbody .wrap hr.wp-header-end').length ? 'hr.wp-header-end' : 'h1' ) ) );
          $(this).show();
        });

      $( '.memberwunder-notice .memberwunder-notice-actions button' ).on( 'click', function( e ){
        e.preventDefault();

        var button = $(this);
        
        $.ajax({
            url: $(this).data( 'dismiss-url' ),
            type: 'GET',
            cache: false,
            timeout: 0,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function( data ) { 
              if( data == 'success' )
                button.closest( '.memberwunder-notice' ).hide();
            },
          });
      });

      $( '.memberwunder-notice.js-memberwunder-notice-ajax .memberwunder-notice-actions a' ).on( 'click', function( e ){
        e.preventDefault();

        var wrapper = $(this).closest('.memberwunder-notice'),
            loader  = wrapper.find( '.memberwunder-notice-actions .memberwunder-notice-actions-loader' );

        if( wrapper.hasClass( 'memberwunder-ajax-go' ) )
          return; 

        $.ajax({
            url: $(this).attr( 'href' ),
            type: 'GET',
            cache: false,
            timeout: 0,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function( data ) { 
              data = JSON.parse( data );

              if( data.status == 'success' )
                wrapper.find('.memberwunder-notice-actions').hide();
              else{
                wrapper.removeClass( 'memberwunder-ajax-go' );
                loader.hide();
              }

              wrapper.find('.memberwunder-notice-text').html( '<p class="memberwunder-notice-text-type-' + data.status + '">' + data.message + '</p>' );
            },
            beforeSend: function(){
              wrapper.addClass( 'memberwunder-ajax-go' );
              loader.show();
            }
          });
      });
    });
})(jQuery);
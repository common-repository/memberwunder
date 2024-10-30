<?php
    namespace MemberWunder\Controller\Options;

    class Currency 
    {
      /**
       * list currencies
       * 
       * @var null
       *
       * @since  1.0.28.8
       * 
       */
      static $currencies = NULL;

      /**
       * init cuttrncies list
       * 
       * @since 1.0.28.8
       * 
       */
      private static function init()
      {
        self::$currencies = array(
                                'eur' =>  array(
                                              'label'   =>  __( 'Euro', TWM_TD ),
                                              'symbol'  =>  '&#8364;',
                                              'format'  =>  '%s %symbol%'
                                              ),
                                'usd' =>  array(
                                              'label'   =>  __( 'Dollar', TWM_TD ),
                                              'symbol'  =>  '&#36;',
                                              'format'  =>  '%symbol% %s'
                                              ),
                                'chf' =>  array(
                                              'label'   =>  __( 'Swiss Franc', TWM_TD ),
                                              'symbol'  =>  'CHF',
                                              'format'  =>  '%s %symbol%'
                                              ),
                                );
      }

      /**
       * check is currencies loaded?
       *
       * @since  1.0.28.8
       * 
       */
      private static function check()
      {
        if( self::$currencies === NULL )
          self::init();
      }

      /**
       * get list of currencies $key => $label
       * 
       * @return array
       *
       * @since  1.0.28.8
       * 
       */
      public static function get_currencies()
      {
        self::check();

        $data = array();

        foreach( self::$currencies as $key => $currency )
          $data[ $key ] = $currency['label'];

        return $data;
      }

      /**
       * get format of price for currency
       * 
       * @param  string $key
       * 
       * @return string
       *
       * @since  1.0.28.8
       * 
       */
      public static function get_currency_format( $key )
      {
        self::check();

        return isset( self::$currencies[ $key ] ) ? str_replace( '%symbol%', self::$currencies[ $key ]['symbol'], self::$currencies[ $key ]['format'] ) : '';
      }
    }
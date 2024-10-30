<?php
  namespace MemberWunder\Helpers;

  class ImportExport
  {
    /**
     * generate ajax link for loading ZIP archive
     * 
     * @return string
     *
     * @since  1.0.34.2
     * 
     */
    public static function ajax_url()
    {
        return twm_is_pro() ? \MemberWunder\Controller\ImportExport\Import::link_to_zip() : '';
    }

    /**
     * return name of field for zip archive
     * 
     * @return string
     *
     * @since  1.0.34.2
     * 
     */
    public static function field_name()
    {
        return twm_is_pro() ? \MemberWunder\Controller\ImportExport\Import::$file_input_name : '';
    }
  }

;
(function ($, document) {

    function init($element) {

        $element.datepicker({
            dateFormat: 'dd.mm.yy'
        });

    }





    $(document).ready(function () {

        $('.js-twm-datapicker').each(function () {
            init($(this));
        });

    });



})(jQuery, document);


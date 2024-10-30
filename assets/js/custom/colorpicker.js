;
(function ($, document) {

    function init($element) {


        var $input = $element.find('.js-twm-colorpicker__input');

        $element.ColorPicker({
            onChange: function (hsb, hex, rgb) {
                $input.val('#' + hex);
                $element.css('background', '#' + hex);
            },
            onSubmit: function (hsb, hex, rgb) {
                $input.val('#' + hex);
                $element.css('background', '#' + hex);
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor($input.val());
            }

        });



    }





    $(document).ready(function () {

        $('.js-twm-colorpicker').each(function () {
            init($(this));
        });

    });



})(jQuery, document);


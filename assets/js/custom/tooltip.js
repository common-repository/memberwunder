(function ($, document) {



    function init($element) {

        var $label = $element.find('> th > label');
        var $input = $element.find('[data-tooltip]');

        var $img = $('<img alt="" class="tooltip-img" src="/wp-content/plugins/tw-membership/assets/css/images/tooltip.png">');

        $label.html($label.html() + '&nbsp;');

        $label.append($img);


        $img.tooltipster({
            content: $input.data('tooltip')

        });

    }

    function initElement($element) {


        var $img = $('<img alt="" class="tooltip-img" src="/wp-content/plugins/tw-membership/assets/css/images/tooltip.png">');

        $element.html($element.html() + '&nbsp;');

        $element.append($img);

        $img.tooltipster({
            content: $element.data('tooltip')

        });

    }

    function initMetabox(id) {

        var $info = $('#' + id).find('.hndle span');
        var $img = $('<img alt="" class="tooltip-img" src="/wp-content/plugins/tw-membership/assets/css/images/tooltip.png">');
        $info.html($info.html() + '&nbsp;');
        $info.append($img);
        $img.tooltipster({
            content: $('#' + id).find('.js-' + id).data('tooltip')
        });

    }


    $(document).ready(function () {
        $('.js-twm-tooltip').each(function () {
            init($(this));
        });

        $('.js-twm-tooltip-element').each(function () {
            initElement($(this));
        });


        initMetabox('twmembership_info');
        initMetabox('twmembership_ds24');
        initMetabox('twmembership_ml');





    });


}(jQuery, document));
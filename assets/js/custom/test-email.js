(function ($, document) {


    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }


    function init($element) {


        var $btn = $element.find('.js-twm-test-email__sbm');
        var $email = $element.find('.js-twm-test-email__email');
        var $subject = $element.find('.js-twm-test-email__subject');
        var $message = $element.find('.js-twm-test-email__message');

        $email.on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $subject.on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $message.on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });



        $btn.on('click', function () {

            var valid = true;

            $email.css('border-color', '#ddd');
            $subject.css('border-color', '#ddd');
            $message.css('border-color', '#ddd');


            if (!$email.val() || !validateEmail($email.val())) {
                $email.css('border-color', 'red');
                valid = false;
            }

            if (!$subject.val()) {
                $subject.css('border-color', 'red');
                valid = false;
            }

            if (!$message.val()) {
                $message.css('border-color', 'red');
                valid = false;
            }

            if (!valid) {
                return;
            }


            $element.html($element.data('wait'));


            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'twm_test_email',
                    email: $email.val(),
                    subject: $subject.val(),
                    message: $message.val(),
                }
            }).done(function (resp) {
                var html = '';

                html = '<p class="twm-smtp-test-' + resp.status + '">' + resp.message + '</p>';
                
                if (resp.resp.debug)
                    html += '<div class="twm-smtp-yellow-box">' +
                            '<textarea rows="20" style="width: 100%;">' + resp.resp.debug + '</textarea>' +
                            '</div>';

                $element.html(html);
            });
        });





    }




    $(document).ready(function () {
        $('.js-twm-test-email').each(function () {
            init($(this));
        });


    });


}(jQuery, document));

//https://www.tutel.me/c/wordpress/tag/wp-editor/page/1

jQuery(function ($) {
    var app = {

        _accm: null,
        _accl: null,
        _accq: null,
        _accqq: null,

        run: function () {
            this._initApp();
        }

        , refresh: function () {
            var _this = this;

            this._initAccordions();

            //init all editors
            $('.js-twmshp__editor:visible').each(function () {
                _this._initEditor($(this).attr('id'));
            });
        }

        , _initApp: function () {
            var _this = this;

            this._initEvents();
            this._initSocial();
            this._initPaymentSystems();

            this.refresh();

            return this;
        }

        , _initTabs: function ($element) {
            var _this = this;
            $element.tabs({
                activate: function(event, ui) {
                    ui.oldTab.removeClass('wp-tab-active');
                    ui.newTab.addClass('wp-tab-active');

                    ui.newPanel.find('.js-twmshp__editor:visible').each(function () {
                        _this._initEditor($(this).attr('id'));
                    });
                },
                beforeActivate: function (event, ui) {
                    ui.oldPanel.find('.js-twmshp__editor').each(function () {
                        _this._removeEditor($(this).attr('id'));
                    });
                },
            });
        }

        , _initEvents: function () {
            var _this = this;

            $(document).on('postbox-toggled', function(e, p) {
                $(p).find('.js-twmshp__editor').each(function () {
                    _this._removeEditor($(this).attr('id'));
                });
                $(p).find('.js-twmshp__editor:visible').each(function () {
                    _this._initEditor($(this).attr('id'));
                });
            });

            $(window).on('initTabs', function(e, param) {
                _this._initTabs(param.$element);
            });

            /*add module*/
            $(document).on('click', '.js-twmshp__modules-add', function (e) {
                e.preventDefault();
                var editor_id = _this._uniqid();
                var module_id = _this._uniqid();
                var lesson_id = _this._uniqid();
                var editor_name = 'lessons_content[' + module_id + '][' + lesson_id + ']';

                var lesson_editor = _this._getEditorHtml(editor_id, editor_name);
                var lesson_template = _this._getLessonHtml(module_id, lesson_id, lesson_editor, '');
                var module_el = $(_this._getModuleHtml(module_id, lesson_template));

                _this._accm.append(module_el);

                _this._initAccordion(_this._accm, true);
                _this._initAccordion(module_el.find('.js-twmshp__modules-item_wrapper-lessons'), true);

                _this._accm.accordion({
                    active: -1
                });
            });

            /*remove module*/
            $(document).on('click', '.js-twmshp__remove-module', function (e) {
                e.preventDefault();

                if (confirm('Remove?')) {
                    if ($('.js-twmshp__modules-item').length > 1) {
                        $(this).closest('.js-twmshp__modules-item').remove();
                        _this.refresh();
                    }
                }
            });

            /*add lesson*/
            $(document).on('click', '.js-twmshp__modules-lessons-add', function (e) {
                e.preventDefault();
                var _that = $(this);
                var elem = _that.closest('.js-twmshp__modules-item_wrapper').find('.js-twmshp__modules-item_wrapper-lessons');

                var editor_id = _this._uniqid();
                var lesson_id = _this._uniqid();
                var module_id = _that.closest('.js-twmshp__modules-item').attr('data-module-id');
                var editor_name = 'lessons_content[' + module_id + '][' + lesson_id + ']';
                var lesson_editor = _this._getEditorHtml(editor_id, editor_name);
                var lesson_el = $(_this._getLessonHtml(module_id, lesson_id, lesson_editor, ''));

                elem.append(lesson_el);

                _this._initAccordion(elem, false);

                elem.accordion({
                    active: -1
                });
            });

            /*remove lesson*/
            $(document).on('click', '.js-twmshp__remove-lesson', function (e) {
                e.preventDefault();
                if (confirm('Remove?')) {
                    if ($('.js-twmshp__lesson-item').length > 1) {
                        var elem = $(this).closest('.js-twmshp__modules-item_wrapper').find('.js-twmshp__modules-item_wrapper-lessons');
                        $(this).closest('.js-twmshp__lesson-item').remove();
                        _this._initAccordion(elem, true);
                    }
                }
            });


            /*add quiz*/
            $(document).on('click', '.js-twmshp__lesson-quizes-add', function (e) {
                e.preventDefault();
                var _that = $(this);
                if (_that.hasClass('disabled')) {
                    return;
                }
                _that.addClass('disabled');

                var elem = _that.closest('.js-twmshp__lesson-form').find('.js-twmshp__wrapper-quizes');

                var editor_id = _this._uniqid();
                var module_id = _that.closest('.js-twmshp__modules-item').attr('data-module-id');
                var lesson_id = _that.closest('.js-twmshp__lesson-item').attr('data-lesson-id');
                var editor_name = 'lessons_quiz[' + module_id + '][' + lesson_id + '][content]';
                var quiz_editor = _this._getEditorHtml(editor_id, editor_name);
                var quiz_template = _this._getQuizHtml(module_id, lesson_id, quiz_editor);

                elem.append(quiz_template);

                _this._initAccordion(elem, false);

                elem.accordion({
                    active: -1
                });
            });

            /*remove quiz*/
            $(document).on('click', '.js-twmshp__remove-quiz', function (e) {
                e.preventDefault();
                if (confirm('Remove?')) {
                    var elem = $(this).closest('.js-twmshp__lesson-form').find('.js-twmshp__wrapper-quizes');
                    $(this).closest('.js-twmshp__lesson-form').find('.js-twmshp__lesson-quizes-add').removeClass('disabled');
                    $(this).closest('.js-twmshp__quiz-item').remove();
                    _this._initAccordion(elem, false);
                }
            });

            /*add quiz question*/
            $(document).on('click', '.js-twmshp__quiz-questions-add', function (e) {
                e.preventDefault();
                var _that = $(this);

                var elem = _that.closest('.js-twmshp__quiz-form').find('.js-twmshp__wrapper-quiz-questions');

                var module_id = _that.closest('.js-twmshp__modules-item').attr('data-module-id');
                var lesson_id = _that.closest('.js-twmshp__lesson-item').attr('data-lesson-id');
                var question_id = _this._uniqid();
                var question_template = _this._getQuizQuestionHtml(module_id, lesson_id, question_id);

                elem.append(question_template);

                _this._initAccordion(elem, true);

                elem.accordion({
                    active: -1
                });
            });

            /*remove quiz question*/
            $(document).on('click', '.js-twmshp__remove-quiz-question', function (e) {
                e.preventDefault();
                if (confirm('Remove?')) {
                    var elem = $(this).closest('.js-twmshp__quiz-form').find('.js-twmshp__wrapper-quiz-questions');
                    $(this).closest('.js-twmshp__quiz-question-item').remove();
                    _this._initAccordion(elem, true);
                }
            });

            /*add quiz question answer*/
            $(document).on('click', '.js-twmshp__quiz-question-answers-add', function (e) {
                e.preventDefault();
                var _that = $(this);

                var elem = _that.closest('.js-twmshp__quiz-question-form').find('.js-twmshp__wrapper-quiz-question-answers');

                var module_id = _that.closest('.js-twmshp__modules-item').attr('data-module-id');
                var lesson_id = _that.closest('.js-twmshp__lesson-item').attr('data-lesson-id');
                var question_id = _that.closest('.js-twmshp__quiz-question-item').attr('data-question-id');
                var answer_id = _this._uniqid();
                var answer_template = _this._getQuizQuestionAnswerHtml(module_id, lesson_id, question_id, answer_id);

                elem.append(answer_template);

                _that.closest('.js-twmshp__quiz-question-form').find('.js-twmshp__quiz-question-type').change();
            });

            /*change quiz question answer type*/
            $(document).on('change', '.js-twmshp__quiz-question-type', function (e) {
                var value = $(this).val();

                var answers = $(this).closest('.js-twmshp__quiz-question-item').find('.js-twmshp__wrapper-quiz-question-answers');
                var answersRows = $(this).closest('.js-twmshp__quiz-question-item').find('.js-twmshp__quiz-question-answer_type');

                answersRows.hide();
                answersRows.filter('.js-twmshp__quiz-question-answer_type_' + value).show();

                if (value === 'single' || value === 'single_image') {
                    answers.find('.js-twmshp__quiz-question-answer_correct').attr('type', 'radio');
                } else if (value === 'multi' || value === 'multi_image') {
                    answers.find('.js-twmshp__quiz-question-answer_correct').attr('type', 'checkbox');
                }
            });

            /*remove quiz question answer*/
            $(document).on('click', '.js-twmshp__remove-quiz-question-answer', function (e) {
                e.preventDefault();
                if (confirm('Remove?')) {
                    $(this).closest('.js-twmshp__quiz-question-answer-item').remove();
                }
            });

            /*Preview title*/
            $(document).on('keyup', '.js-twmshp__modules-item-title, .js-twmshp__lesson-title, .js-twmshp__quiz-title, .js-twmshp__quiz-question-title', function (e) {
                var val = $(this).val();
                if ($.trim(val) == '') {
                    val = 'New';
                }
                $(this).closest('.group').find('.js-group__label_text:eq(0)').text(val);
                _this._accm.accordion("refresh");
                _this._accl.accordion("refresh");
                _this._accq.accordion("refresh");
                _this._accqq.accordion("refresh");
            });

            /*Template selection*/
            $(document).on('click', '.js-twmshp__settings_template', function (e) {
                var val = $(this).val();
                if (val === 'custom') {
                    $('.js-twmshp__settings_colors').show();
                } else {
                    $('.js-twmshp__settings_colors').hide();
                }
            });

            /*Social selection*/
            $(document).on('click', '.js-twmshp__social-add', function (e) {
                e.preventDefault();
                var wrapper = $(this).closest('.js-twmshp__social-wrapper');
                var container = wrapper.find('.js-twmshp__social');
                var html = wrapper.find('.js-twmshp__social-item-tpl').html();
                html = _this._replaceAll(html, '{%image%}', '');
                html = _this._replaceAll(html, '{%url%}', '');
                html = _this._replaceAll(html, '{%label%}', '');
                html = _this._replaceAll(html, '{%description%}', '');
                container.append(html);
            });
            $(document).on('click', '.js-twmshp__social-delete', function (e) {
                e.preventDefault();
                var item = $(this).closest('.js-twmshp__social-item');
                item.remove();
            });

            /*Media selection*/
            var file_frame = null;
            $(document).on('click', '.js-twmshp__image_upload_button', function (e) {
                e.preventDefault();

                var $wrapper = $(this).closest('.js-twmshp__image_upload_wrapper');
                var size = $(this).data('size');

                if (!file_frame) {
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: 'Select an image to upload',
                        button: {text: 'Use this image'},
                        multiple: false
                    });
                }

                file_frame.off('select');
                file_frame.on('select', function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    var url = attachment.url;

                    if (size && attachment.hasOwnProperty('sizes') && attachment.sizes.hasOwnProperty(size)) {
                        url = attachment.sizes[size].url;
                    }

                    $wrapper.find('.js-twmshp__image_upload_value').val(url);
                });
                file_frame.open();
            });
        }

        , _initSocial: function () {
            var _this = this;

            $('.js-twmshp__social').each(function () {
                var container = $(this);
                var wrapper = container.closest('.js-twmshp__social-wrapper');
                var html = wrapper.find('.js-twmshp__social-item-tpl').html();

                var items = [];

                container.find('input[name="twmembership[social][image][]"]').each(function (index) {
                    if (index >= items.length) {
                        items.push({});
                    }
                    items[index].image = $(this).val();
                });
                container.find('input[name="twmembership[social][url][]"]').each(function (index) {
                    if (index >= items.length) {
                        items.push({});
                    }
                    items[index].url = $(this).val();
                });
                container.find('input[name="twmembership[social][label][]"]').each(function (index) {
                    if (index >= items.length) {
                        items.push({});
                    }
                    items[index].label = $(this).val();
                });
                container.find('input[name="twmembership[social][description][]"]').each(function (index) {
                    if (index >= items.length) {
                        items.push({});
                    }
                    items[index].description = $(this).val();
                });
                container.empty();

                $.each(items, function (index, item) {
                    itemHtml = _this._replaceAll(html, '{%image%}', _this._escapeHtml(item.image));
                    itemHtml = _this._replaceAll(itemHtml, '{%url%}', _this._escapeHtml(item.url));
                    itemHtml = _this._replaceAll(itemHtml, '{%label%}', _this._escapeHtml(item.label));
                    itemHtml = _this._replaceAll(itemHtml, '{%description%}', _this._escapeHtml(item.description));
                    container.append(itemHtml);
                });
            }).show();

            $('.js-twmshp__social-actions').show();
        }

        , _initPaymentSystems: function () {
            var _this = this;

            var wrapper = $('#ds24-existing');
            var product_content = $('#wmshp-ds24-product-content');
            var product_no_results = $('#wmshp-ds24-product-no-results');
            var product_spinner = $('#wmshp-ds24-product-spinner');
            product_spinner.parent().addClass('loading-content');
            product_content.hide();
            product_no_results.hide();

            $(document).on('change', '.js-twmshp__ds24-salespage-type', function (e) {
                var type = $(this).val();
                if (type === 'custom') {
                    $('.js-twmshp__ds24-salespage-type-custom').show();
                    $('.js-twmshp__ds24-salespage-type-default').hide();
                } else {
                    $('.js-twmshp__ds24-salespage-type-custom').hide();
                    $('.js-twmshp__ds24-salespage-type-default').show();
                }
            });

            $.post(ajaxurl, {'action': 'twm_list_ds24_products'}, function (response) {
                if (!response.success) {
                    return;
                }

                var el = $('#twmshp-ds24-product');
                var el_product_ids = $('input[name="ds24[product_ids][]"]');

                var product_ids = [];
                el_product_ids.each(function() {
                    product_ids.push($(this).val());
                });

                if (response.data && response.data.length > 0) {
                    var tpl = el.html();
                    var html = '';
                    var product_html, checked;
                    for (var i = 0, l = response.data.length; i < l; i++) {
                        checked = $.inArray(response.data[i].id, product_ids) !== -1;

                        product_html = _this._replaceAll(tpl, '{%id%}', _this._escapeHtml(response.data[i].id));
                        product_html = _this._replaceAll(product_html, '{%name%}', _this._escapeHtml(response.data[i].name));
                        product_html = _this._replaceAll(product_html, '{%checked%}', checked ? 'checked="checked"' : '');
                        html += product_html;
                    }

                    product_content.html(html).show();

                    var checked_input = product_content.find('input[checked]').first();
                    if (checked_input.length > 0) {
                        wrapper.find('.wp-tab-panel').scrollTop(
                            Math.max(checked_input[0].offsetTop - (wrapper.height() + checked_input.height()) / 2, 0)
                        );
                    }

                    el_product_ids.remove();

                    var refreshSalespageProduct = function () {
                        var salespage_product_id = $('[name="ds24[salespage_product_id]"]:enabled').last().val();
                        var el_salespage_product = $('.js-twmshp__ds24-salespage-product');

                        el_salespage_product.empty();
                        $('input[name="ds24[product_ids][]"]:checked').each(function() {
                            var id = $(this).val();
                            var title = $(this).data('title');
                            var el_option = $('<option></option>').val(id).text(title);
                            if (id == salespage_product_id) {
                                el_option.prop('selected', true)
                            }
                            el_salespage_product.append(el_option);
                        });

                        el_salespage_product.prop('disabled', false);
                    };
                    refreshSalespageProduct();

                    $(document).on('click', '.js-twmshp__ds24-product_id', refreshSalespageProduct);
                } else {
                    product_no_results.show();
                }

                product_spinner.parent().removeClass('loading-content');
            });

        }

        , _initAccordion: function (acc, sortable) {
            var _this = this;

            acc.accordion({
                heightStyle: "content",
                active: false,
                collapsible: true,
                activate: function (event, ui) {
                    ui.newPanel.find('.js-twmshp__editor:visible').each(function () {
                        _this._initEditor($(this).attr('id'));
                    });
                },
                beforeActivate: function (event, ui) {
                    if (event.originalEvent && event.originalEvent.target) {
                        var target = $(event.originalEvent.target);
                        if (target.is('.ui-icon-trash,a')) {
                            return false;
                        }
                    }
                    ui.oldPanel.find('.js-twmshp__editor').each(function () {
                        _this._removeEditor($(this).attr('id'));
                    });
                },
                icons: {
                    header: 'ui-icon-triangle-1-e',
                    activeHeader: 'ui-icon-triangle-1-n'
                },
                header: "> div > h3"
            });

            if (sortable) {
                acc.sortable({
                    axis: "y",
                    handle: "h3",
                    start: function (event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
                        $(ui.item).find('.js-twmshp__editor').each(function () {
                            _this._removeEditor($(this).attr('id'));
                        });
                    },
                    stop: function (event, ui) {

                        // IE doesn't register the blur when sorting
                        // so trigger focusout handlers to remove .ui-state-focus
                        ui.item.children("h3").triggerHandler("focusout");
                        // Refresh accordion to handle new order
                        $(this).accordion("refresh");

                        $(ui.item).find('.js-twmshp__editor:visible').each(function () {
                            _this._initEditor($(this).attr('id'));
                        });

                        $(this).sortable("refresh");
                    }
                });
            }

            acc.accordion("refresh");
        }


        , _initAccordions: function () {
            this._accm = $(".js-twmshp__modules");
            this._initAccordion(this._accm, true);

            this._accl = $(".js-twmshp__modules-item_wrapper-lessons");
            this._initAccordion(this._accl, true);

            this._accq = $(".js-twmshp__wrapper-quizes");
            this._initAccordion(this._accq, false);

            this._accqq = $(".js-twmshp__wrapper-quiz-questions");
            this._initAccordion(this._accqq, true);

            return this;
        }

        , _replaceAll: function (str, toReplace, replaceWith) {
            return str ? str.split(toReplace).join(replaceWith) : '';
        }
        , _uniqid: function () {
            return '_' + Math.random().toString(36).substr(2, 9);
        }

        , _escapeHtml: function (text) {
            return text ? text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;') : '';
        }

        , _initEditor: function (editor_id) {
            if (tinyMCEPreInit.mceInit.hasOwnProperty(editor_id)) {
                tinymce.execCommand('mceRemoveEditor', true, editor_id);
                tinymce.execCommand('mceAddEditor', true, editor_id);
            } else {
                window.quicktags && quicktags({id : editor_id});

                tinyMCEPreInit.mceInit[editor_id] = tinymce.extend(
                    {},
                    tinyMCEPreInit.mceInit['content'],
                    {
                        toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,spellchecker,wp_adv',
                        selector: '#' + editor_id
                    }
                );

                try { tinymce.init( tinyMCEPreInit.mceInit[editor_id] ); } catch(e){}
            }

            return this;
        }
        , _removeEditor: function (editor_id) {
            tinymce.execCommand('mceRemoveEditor', true, editor_id);
            return this;
        }
        , _getEditorHtml: function (editor_id, name) {
            var _this = this;
            var tpl = $('#twmshp-editor').html();
            tpl = this._replaceAll(tpl, '{%id%}', editor_id);
            tpl = this._replaceAll(tpl, '{%name%}', name);
            return tpl;
        }
        , _getLessonHtml: function (module_id, lesson_id, editor_html, quizes_html) {
            var _this = this;
            var tpl = $('#twmshp-accordion__lesson-template').html();
            tpl = this._replaceAll(tpl, '{%module_id%}', module_id);
            tpl = this._replaceAll(tpl, '{%lesson_id%}', lesson_id);
            tpl = this._replaceAll(tpl, '{%editor%}', editor_html);
            tpl = this._replaceAll(tpl, '{%quizes%}', quizes_html);
            return tpl;
        }, _getQuizHtml: function (module_id, lesson_id, editor_html) {
            var _this = this;
            var tpl = $('#twmshp-accordion__quiz-template').html();
            tpl = this._replaceAll(tpl, '{%module_id%}', module_id);
            tpl = this._replaceAll(tpl, '{%lesson_id%}', lesson_id);
            tpl = this._replaceAll(tpl, '{%editor%}', editor_html);
            return tpl;
        }, _getQuizQuestionHtml: function (module_id, lesson_id, question_id) {
            var _this = this;
            var tpl = $('#twmshp-accordion__quiz-question-template').html();
            tpl = this._replaceAll(tpl, '{%module_id%}', module_id);
            tpl = this._replaceAll(tpl, '{%lesson_id%}', lesson_id);
            tpl = this._replaceAll(tpl, '{%question_id%}', question_id);
            return tpl;
        }, _getQuizQuestionAnswerHtml: function (module_id, lesson_id, question_id, answer_id) {
            var _this = this;
            var tpl = $('#twmshp-accordion__quiz-question-answer-template').html();
            tpl = this._replaceAll(tpl, '{%module_id%}', module_id);
            tpl = this._replaceAll(tpl, '{%lesson_id%}', lesson_id);
            tpl = this._replaceAll(tpl, '{%question_id%}', question_id);
            tpl = this._replaceAll(tpl, '{%answer_id%}', answer_id);
            return tpl;
        }, _getModuleHtml: function (module_id, lessons_html) {
            var _this = this;
            var tpl = $('#twmshp-accordion__module-template').html();
            tpl = this._replaceAll(tpl, '{%module_id%}', module_id);
            tpl = this._replaceAll(tpl, '{%lessons%}', lessons_html);
            return tpl;
        }
    };
    app.run();
});
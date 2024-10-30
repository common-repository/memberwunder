
;
(function ($) {

    TW_metabox = {

        /*-----------------------------------------------------------------------------------*/
        /* Repeatable TinyMCE-enhanced textareas
         /*-----------------------------------------------------------------------------------*/

        runTinyMCE: function ($textareas) {

            // some settings for a more minimal tinyMCE editor
            tinyMCEminConfig = {
                theme: "advanced",
                skin: "wp_theme",
                mode: "exact",
                language: "en",
                theme_advanced_resizing: "1",
                width: "100%",
                height: "250",
                theme_advanced_layout_manager: "SimpleLayout",
                theme_advanced_toolbar_location: "top",
                theme_advanced_toolbar_align: "left",
                theme_advanced_buttons1: "styleselect,formatselect,bold,italic,strikethrough,underline,|,link,unlink,|,forecolor,|undo,redo,|,code",
                theme_advanced_buttons2: "",
                theme_advanced_buttons3: "",
                theme_advanced_statusbar_location: "",
                remove_linebreaks: true,
                tabfocus_elements:"content-html,save-post",
                plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview"
            };

            if (tinyMCEbackupConfig === null)
            {
                tinyMCEbackupConfig = $.extend(true, {}, tinyMCE.settings);
            }

            //store the default settings
            try {
                tinyMCEdefaultConfig = $.extend(true, {}, tinyMCE.settings);

                //tweak the setting just a litte to set the height and to add an HTML code button (since toggling editors is crazy difficult)
                tinyMCEdefaultConfig.height = "250";
                tinyMCEdefaultConfig.theme_advanced_buttons1 = tinyMCEdefaultConfig.theme_advanced_buttons1 + ',|,code';

            } catch (e) {
                tinyMCEdefaultConfig = tinyMCEminConfig;
            }

            $textareas.each(function () {

                //give each a unique ID so we can apply TinyMCE to each 
                var id = $(this).attr('id');

                try {
                    //if the customEditor div has the minimal class, serve up the minimal tinyMCE configuration
                    if ($(this).parent().hasClass('minimal')) {
                        tinyMCE.settings = tinyMCEminConfig;
                    } else {
                        tinyMCE.settings = tinyMCEdefaultConfig;
                    }

                    //var options  = $(this).getDatas();
                    //options      = vp.parseOpt(options.opt);

                    var options = [];
                    options.use_external_plugins = options.use_external_plugins ? true : false;

                    var plugins = tinyMCE.settings.plugins;
                    var theme_advanced_buttons1 = tinyMCE.settings.theme_advanced_buttons1;

                    // remove `wpfullscreen` plugin
                    plugins = plugins.replace(/,wpfullscreen/gm, '');
                    // remove `wp_fullscreen` button
                    theme_advanced_buttons1 = theme_advanced_buttons1.replace(/wp_fullscreen/gm, '');

                    if (options.use_external_plugins === false)
                    {
                        plugins = plugins.replace(/\-(.*?)(,|$)+?/gm, '');
                    } else
                    {
                        var dep = options.disabled_externals_plugins,
                                dip = options.disabled_internals_plugins,
                                reg;

                        dep = dep.trim();
                        dep = dep.split(/[\s,]+/).join("|");
                        if (dep !== "")
                        {
                            reg = new RegExp('\\-(' + dep + ')(,|$)+?', 'gmi');
                            plugins = plugins.replace(reg, '');
                        }

                        dip = dip.trim();
                        dip = dip.split(/[\s,]+/).join("|");
                        if (dip !== "")
                        {
                            reg = new RegExp('\\-(' + dip + ')(,|$)+?', 'gmi');
                            plugins = plugins.replace(reg, '');
                        }
                    }
                    tinyMCE.settings.plugins = plugins+',code';
                    
                    alert(tinyMCE.settings.plugins);
                    
                    tinyMCE.settings.theme_advanced_buttons1 = theme_advanced_buttons1;

                    setTimeout(function(){
                        tinyMCE.execCommand('mceAddEditor', true, id);
                    },100);
                } catch (e) {
                    console.log(e);
                }

            });
            // restore default settings
            tinyMCE.settings = $.extend(true, {}, tinyMCEbackupConfig);


            return this;
        }, //end runTinyMCE text areas 

        /*-----------------------------------------------------------------------------------*/
        /* Custom Media Upload Buttons for tinyMCE textareas
         /*-----------------------------------------------------------------------------------*/

        mediaButtons: function () {

            $(document).on('click', '.custom_upload_buttons a', function () {
                textarea = $(this).closest('.customEditor').find('textarea');
                mceID = textarea.attr('id');
                kia_backup = window.send_to_editor; // backup the original 'send_to_editor' function
                window.send_to_editor = window.send_to_editor_clone;
                tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            });

            //borrow the send to editor function
            window.send_to_editor_clone = function (html) {

                try {
                    tinyMCE.get(mceID).insertContent(html);
                } catch (e) {
                    $(textarea).insertAtCaret(html);
                }

                tb_remove();

                // restore the default behavior
                window.send_to_editor = kia_backup;
            };
            return this;
        } //end mediaButtons

    }; // End KIA_metabox Object // Don't remove this, or the sky will fall on your head.

})(jQuery);
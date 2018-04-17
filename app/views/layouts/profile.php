<!doctype html>
<!--[if lte IE 9]>     <html lang="en" class="no-focus lt-ie10 lt-ie10-msg"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en" class="no-focus"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <title>Core PHP &amp; UI Framework</title>

    <meta name="robots" content="noindex, nofollow">

    <link rel="shortcut icon" href="/public/assets/img/favicons/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/public/assets/img/favicons/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/public/assets/img/favicons/apple-touch-icon-180x180.png">

    <link rel="stylesheet" href="/public/assets/css/bootstrap.min.css">
    <link rel="stylesheet" id="css-main2" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" id="css-main" href="/public/assets/css/codebase.min.css">


    <script src="/public/assets/js/core/jquery.min.js"></script>
    <script src="/public/assets/js/core/popper.min.js"></script>
    <script src="/public/assets/js/core/bootstrap.min.js"></script>

    <style>
        .non { display: none; } /* данный стиль служит для скрытия из вида и вообще из шаблона элементов */
        .center{
            text-align: center;
        }
    </style>
</head>

<body>
<div id="page-container" class="sidebar-mini side-scroll page-header-fixed page-header-glass main-content-boxed">

    <?= \app\widgets\AssisdeWidget::widget(); ?>
    <?= \app\widgets\SidebarWidget::widget(); ?>
    <?= \app\widgets\HeaderWidget::widget(); ?>

    <main id="main-container">
        <?= $content; ?>
    </main>

    <?= \app\widgets\FooterWidget::widget(); ?>
</div>

<script src="/public/assets/js/core/jquery.slimscroll.min.js"></script>
<script src="/public/assets/js/core/jquery.scrollLock.min.js"></script>
<script src="/public/assets/js/core/jquery.appear.min.js"></script>
<script src="/public/assets/js/core/jquery.countTo.min.js"></script>
<script src="/public/assets/js/core/js.cookie.min.js"></script>
<script src="/public/assets/js/codebase.js"></script>

<link href="/public/assets/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
<script src="/public/assets/bootstrap3-editable/js/bootstrap-editable.js"></script>

<script src="/public/assets/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/bootstrap-wysihtml5-0.0.2.min.js"></script>
<script src="/public/assets/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/wysihtml5-0.3.0.js"></script>


<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.js"></script>

<script>
    !(function($) {
        'use strict';

        var script = {};

        script.ajax = {
            errorMessage: tr('error:ajax-request'),

            ajaxSettings: {
                $loaderEl: $('#loader'),

                beforeSend: function() {
                    this.$loaderEl.show();
                },

                complete: function() {
                    this.$loaderEl.hide();
                }
            },

            init: function() {
                $.ajaxSetup(this.ajaxSettings);
            },

            callbacks: {
                login: function ($form, data) {
                    if (data.status === 'ok') {
                        if (data.data && data.data.redirect) {
                            window.location.href = data.data.redirect;
                        }
                    }
                },

                logout: function ($form, data) {
                    if (data.status === 'ok') {
                        if (data.data && data.data.redirect) {
                            window.location.href = data.data.redirect;
                        }
                    }
                },

                register: function ($form, data) {
                    if (data.status === 'ok') {
                        if (data.data && data.data.redirect) {
                            window.location.href = data.data.redirect;
                        }
                    }
                }

            }
        };

        script.ajaxform = {
            $forms: null,

            init: function(selector) {
                this.$forms = $(selector);
                this.$forms.attr('novalidate', 'novalidate');
                this.initHandler();
            },

            initHandler: function() {
                this.$forms.submit(function() {
                    var $_this = $(this);
                    var result = script.ajaxform.validate($_this);

                    if (result === true) {
                        script.ajaxform.go($_this);
                    }

                    return false;
                });
            },

            callback: function($form, data) {
                var action = getURLParam('act', $form.attr('action'));

                if (!action || action === ';') {
                    action = $form.find('[name|="act"]').val();
                }

                if (action !== ';' && action.length) {
                    var callbackFunction = script.ajax.callbacks[action];
                    if (typeof callbackFunction === 'function') {
                        callbackFunction.call(this, $form, data);
                    }
                }
            },

            validate: function($form) {
                var $fields = $form.find(':input'),
                    isValid = true,
                    $e;

                $fields.each(function(i, e) {
                    $(e).removeClass('error-message');
                });

                $form.find('.error-message').remove();

                $fields.each(function(i, e) {
                    if (e.hasAttribute('required')) {
                        $e = $(e);
                        if (!$e.val().trim()) {
                            $e.addClass('error-message').focus();
                            isValid = false;
                            return isValid;
                        }
                    }
                });
                return isValid;
            },

            go: function($form) {
                var method = $form.attr('method') || 'GET';
                var action = $form.attr('action') || '.';

                var ajaxSettings = {
                    type: method,
                    url: action,
                    data: $form.serialize()
                };

                var $formInputs = $form.find('input,textarea,select');
                $formInputs.attr('readonly', 'readonly');

                var $formButtons = $form.find(':button,input[type="submit"]');

                $formButtons.attr("disabled", "disabled");

                ajaxSettings.complete = function() {
                    script.ajax.ajaxSettings.$loaderEl.hide();
                    $formInputs.removeAttr('readonly');
                    $formButtons.removeAttr('disabled');
                };

                ajaxSettings.success = function(response) {
                    var data;
                    if (typeof response === 'object') {
                        // mime type application/json responsed
                        data = response;
                    } else {
                        try {
                            data = JSON.parse(response);
                        } catch (e) {
                            window.alert(tr('error:form-submit'));
                            return;
                        }
                    }
                    script.ajaxform.validateByAjax($form, data);
                };

                $.ajax(ajaxSettings);
            },

            validateByAjax: function($form, data) {
                if (data.status === 'ok') {
                    if (data.message !== undefined && data.message !== null) {
                        // window.alert(data.message);
                    }
                    if (data.redirect === true) {
                        if (data.url !== undefined && data.url !== null) {
                            window.location.href = data.url;
                        } else {
                            window.location.reload();
                        }
                    }
                } else if (data.status === 'err') {
                    var $mainErrorContainer = $form.find('.main-error');
                    if (data.code === 'main') {
                        if ($mainErrorContainer !== null) {
                            $('#errorContainer').removeClass('hide');
                            $mainErrorContainer.html('<p class="error">' + data.message + '</p>');
                        } else {
                            $form.append('<p class="error">' + data.message + '</p>');
                        }
                    } else {
                        var $errField = $form.find('[name|="' + data.code + '"]');
                        $mainErrorContainer.html(data.message).show();
                        $errField.focus();
                    }
                }

                this.callback($form, data);
            }
        };

        function getURLParam(name, url) {
            url = url || window.location.href;
            name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
            var regexS = "[\\?&]" + name + "=([^&#]*)",
                regex = new RegExp(regexS),
                results = regex.exec(url);

            if (results === undefined || results === null) {
                return ';';
            } else {
                return decodeURIComponent(results[1].replace(/\+/g, " "));
            }
        }

        function tr(params, lang) {
            var messages = {}, translated = '',
                code;

            messages.ru_RU = {
                'error': {
                    'form-submit': 'Произошла ошибка при отправке формы',
                    'ajax-request': 'Произошла ошибка при отправке запроса'
                },

                'notice': {
                    'confirm': 'Подтвердите действие'
                }
            };
            lang = lang || 'ru_RU';
            params = params.toLowerCase().split(':');
            if (messages[lang] !== undefined && params.length) {
                for (var i = 0, msgcat = messages[lang]; i < params.length; i++) {
                    code = params[i];
                    if (typeof msgcat[code] === 'object') {
                        msgcat = msgcat[code];
                    }
                    if (typeof msgcat[code] === 'string') {
                        translated = msgcat[code];
                        break;
                    }
                }
            }
            return translated;
        }

        script.init = function() {
            this.ajaxform.init('form.ajax');
            this.ajax.init();
        };

        $(document).ready(function($) {
            script.init();
        });
    })(jQuery);

</script>
</body>
</html>

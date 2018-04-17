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

    <?= \app\widgets\AssisdeWidget::widget(['widgetVariable' => 1]); ?>
    <?= \app\widgets\SidebarWidget::widget(); ?>
    <?= \app\widgets\HeaderWidget::widget(); ?>

    <main id="main-container">
        <div class="bg-image" style="background-image: url('/public/assets/img/photos/photo21@2x.jpg');">
            <div class="bg-white-op-90">
                <div class="content content-full content-top">
                    <h1 class="py-50 text-center">Welcome, <?= \core\Core::$app->user->getUser()->username; ?>!</h1>
                </div>
            </div>
        </div>
        <div class="content">

            <?= $content; ?>

        </div>
    </main>

    <?= \app\widgets\FooterWidget::widget(); ?>
</div>

<script src="/public/assets/js/core/jquery.slimscroll.min.js"></script>
<script src="/public/assets/js/core/jquery.scrollLock.min.js"></script>
<script src="/public/assets/js/core/jquery.appear.min.js"></script>
<script src="/public/assets/js/core/jquery.countTo.min.js"></script>
<script src="/public/assets/js/core/js.cookie.min.js"></script>
<script src="/public/assets/js/codebase.js"></script>
<script src="/public/assets/js/serialize.js"></script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.js"></script>

<script src="/public/assets/js/auth/auth.js"></script>

</body>
</html>

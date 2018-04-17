<!-- Footer -->
<footer id="page-footer" class="opacity-0">
    <?php if(\core\Core::$app->uri->controller == 'index'): ?>
    <div class="container"><br><br>
        <div class="container">
            <?= 'Index Page'; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="content py-20 font-size-xs clearfix">
        <div class="float-right">
            Crafted with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="" target="_blank">ZikWall</a>
        </div>
        <div class="float-left">
            <?= \core\helpers\Html::a('PHP Core SimpleMVC Framework', '', [
                    'class' => 'font-w600',
                    'linkOptions' => [
                        'target' =>  '_blank'
                    ]
            ])?>
        </div>
    </div>
</footer>
<!-- END Footer -->
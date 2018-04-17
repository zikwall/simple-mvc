<?php
/**
 * @var $elements array
 */
?>
<!-- Side Navigation -->
<div class="content-side content-side-full">
    <ul class="nav-main">
        <?php foreach ($elements as $element => $subMenuElements): ?>
            <li>
                <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="<?= $subMenuElements['icon']; ?>"></i>
                    <span class="sidebar-mini-hide"><?= $element; ?></span>
                </a>
                <ul>
                    <?php foreach ($subMenuElements['items'] as $menuElement => $elementUrl): ?>
                        <li>
                            <a href="<?= $elementUrl; ?>"><?= $menuElement; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<!-- END Side Navigation -->
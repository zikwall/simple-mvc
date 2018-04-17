<?php

namespace app\widgets;


use core\base\Widget;

class FooterWidget extends Widget
{
    public function run()
    {
        $this->render('footerWidget', []);
    }
}
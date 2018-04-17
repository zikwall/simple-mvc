<?php

namespace app\widgets;


use core\base\Widget;

class HeaderWidget extends Widget
{
    public function run()
    {
        $this->render('headerWidget', []);
    }
}
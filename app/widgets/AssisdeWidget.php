<?php

namespace app\widgets;

use core\base\Widget;

class AssisdeWidget extends Widget
{
    public $widgetVariable;
    public $test = 'notEventValue';

    public function run()
    {
        $this->render('assideWidget', [
            'testss' => $this->test
        ]);
    }
}
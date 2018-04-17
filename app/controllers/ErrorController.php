<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.01.2018
 * Time: 19:31
 */

namespace app\controllers;


use core\base\BaseErrorController;

class ErrorController extends BaseErrorController
{
    public function action404()
    {
        $this->notFound();
        return $this->view->render('404');
    }

    public function action400()
    {
        $this->badRequest();
        return $this->view->render('400');
    }
}
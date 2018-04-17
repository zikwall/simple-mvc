<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.01.2018
 * Time: 19:30
 */

namespace core\base;


class BaseErrorController extends Controller
{
    public function beforeAction()
    {
        $this->setLayout('error');
        return parent::beforeAction();
    }

    public function notFound()
    {
        header('HTTP/1.0 404 Not Found', true, 404);
    }

    public function badRequest()
    {
        header('HTTP/1.0 400 Bad Request', true, 400);
    }
}
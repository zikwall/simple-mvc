<?php

namespace app\controllers;

use core\auth\AuthorizationAjaxRequest;
use core\base\Controller;
use app\models\User;
use core\Core;

class AuthController extends Controller
{
    public function beforeAction()
    {
        if (User::isAuthorized()){
            return $this->goHome();
        }

        $this->setLayout('auth');
        return parent::beforeAction();
    }

    public function actionAjax()
    {
        if (!empty($_COOKIE['sid'])) {
            session_id($_COOKIE['sid']);
        }

        session_start();

        $ajaxRequest = new AuthorizationAjaxRequest($_REQUEST);
        $ajaxRequest->showResponse();
    }

    public function actionLogin()
    {
        return $this->render('login', []);
    }

    public function actionSignup()
    {
        return $this->render('signup', []);
    }

    public function actionLogout()
    {
        if(Core::$app->request->getIsPost()){
            (new User())->logout();
        }
    }
}
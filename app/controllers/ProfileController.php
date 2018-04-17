<?php

namespace app\controllers;

use core\base\BaseAuthorizationController;
use core\Core;

class ProfileController extends BaseAuthorizationController
{
    public function beforeAction()
    {
        $this->setLayout('profile');

        return parent::beforeAction();
    }

    public function actionIndex()
    {
        return $this->render('profilePage', [
            'username' => Core::$app->user->getUser()->username
        ]);
    }

    public function actionUser($id)
    {
        return $this->render('profilePage', [
            'username' => Core::$app->user->getUserName($id)
        ]);
    }
}
<?php

namespace core\base;

use core\user\BaseUser;

class BaseAuthorizationController extends Controller
{
    public function beforeAction()
    {
        if (!BaseUser::isAuthorized()){
            return $this->goLogin();
        }
        return parent::beforeAction();
    }
}
<?php

namespace app\controllers;

use app\models\User;
use app\models\UserModel;
use core\base\Controller;
use core\db\Builder;

class UserController extends Controller
{
    /**
     *  simple CRUD for users
     */
    public function actionIndex()
    {
        $user = new UserModel();
        $builder = new Builder();

        // update user
        /*$userUpd = (new UserModel(1))->update(['email' => 'zikwall@gmail.com']);*/

        // create user
        /*$userCreate = new UserModel();
        $userCreate->username  = 'zikwall';
        $userCreate->email = 'zikwall@gmail.com';
        $hash = (new User())->passwordHash('mysimplepassword');
        $userCreate->password = $hash['hash'];
        $userCreate->salt = $hash['salt'];
        $userCreate->save();*/

        $user->where('id', 'in', [2, 3, 5])->find();
        $userData = $user->fetchAll();

        return $this->render('index', [
            'user' => $userData
        ]);
    }
}
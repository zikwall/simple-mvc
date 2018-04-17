<?php
 return [
     'db' => [
         'host' => 'localhost',
         'user' => 'gavrik',
         'password' => 'gavrik',
         'dbname' => 'cellbrush',
         'pref' => ''
     ],
     'routes' => [
         // 'url' => 'controller/action/param1/param2/.../param(n)'
         // '/my/controller/param1/:num/param2/:any/param3/:num-:num' => MyController/actionMy/$1/$2/$3/$4

         # INDEX
         '/'             => 'IndexController/actionIndex',
         '/index'        => 'IndexController/actionIndex',
         '/index:any'    => 'IndexController/actionIndex',

         # AUTH
         '/auth/login'   => 'AuthController/actionLogin',
         '/auth/signup'  => 'AuthController/actionSignup',
         '/auth/logout' => 'AuthController/actionLogout',
         '/auth/ajax'    => 'AuthController/actionAjax',

         # USER
         '/profile'        => 'ProfileController/actionIndex',
         '/profile/u/:num' => 'ProfileController/actionUser/$1',
         '/user/index' => 'UserController/actionIndex',

         '/:any' => 'ErrorController/action404',
     ]
 ];
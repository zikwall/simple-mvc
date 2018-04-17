<?php

namespace app\widgets;


use core\base\Widget;

class SidebarWidget extends Widget
{
    public static function getSidebarElements()
    {
        return [
            'Панель управления' => [
                'icon' => 'si si-cup',
                'items' => [
                    'Главная' => '/',
                    'О Компоненте' => '/component/about',
                    'Шаблоны' => '/template/list',
                    'Отчеты' => '/report/list',
                    'Создать матричный шаблон' => '/template/create/matrix',
                    'Создать табличный шаблон' => '/template/create/flat'
                ],
            ],
            'Моё, моё, моё' => [
                'icon' => 'si si-puzzle',
                'items' => [
                    'Профиль' => '/profile',
                    'Мои шаблоны' => '/template/my/list',
                    'Мои отчеты' => '/report/my/list',
                ],
            ],
        ];
    }

    public function run()
    {
        $this->render('sidebarWidget', [
            'elements' => static::getSidebarElements()
        ]);
    }
}
<?php

namespace app\controllers;

use app\components\cellconstructor\constructors\ContentConstructor;
use app\components\cellconstructor\constructors\TableConstructor;
use app\components\cellconstructor\helpers\Helper;
use core\base\BaseAuthorizationController;
use core\Core;
use core\helpers\Json;

class IndexController extends BaseAuthorizationController
{
    public function actionIndex()
    {
        //$constructor = new TableConstructor();
        //$report = $constructor->getContentContainer()->findReport(8);
        //$template = $constructor->getContentContainer()->findTemplate($report['template_id']);

        //Helper::p($constructor->arrayTree(Json::decode(file_get_contents('Up_trueArray.json'))));
        //Helper::p($constructor->getContentContainer()->checkMatrixRecord('carsStorange', 'F3.F10.F9', 'F16.F18.F21'));
        //Helper::p($constructor->getContentContainer()->findReports(true));
        //Helper::p($constructor->getContentContainer()->findReport(1, true));
        //Helper::p($constructor->getContentContainer()->findTemplates());
        //Helper::p($q = $constructor->getContentContainer()->findTemplate(8));
        //Helper::p($q = $constructor->getContentContainer()->findTemplateReports(1));
        //$xt = Json::decode(file_get_contents('cars_trueArray.json'));
        //$yt = Json::decode(file_get_contents('cars_trueArray.json'));
        //$x = Json::decode(file_get_contents('LH.json'));
        //$y = Json::decode(file_get_contents('upH.json'));
        //$lastUp = $constructor->getArrayTreeLastLevelElements($y);
        //$constructor->unsetStorange();
        //$lastLeft = $constructor->getArrayTreeLastLevelElements($x);
        //Helper::p($lastLeft);
        //$constructor->getContentConstructor()->commandCreateMatrixStorange($lastUp, $lastLeft, 'testMatrix1');
        //return $this->render('index', []);
        //$constructor->getContentConstructor()->commandSaveMatrixTemplate('testSimmulateSaves1', $y, $x);
        //echo $table = $constructor->drawMatrixTable(($y),($x),'testMatrix1', true)->render();
        //return $this->render('index', ['table' => $table]);
        //$x = Json::decode(file_get_contents('cars_LH.json'));
        //$y = Json::decode(file_get_contents('cars_UPH.json'));
        //$constructor = new TableConstructor();

        //$lastUp = $constructor->getArrayTreeLastLevelElements($y);
        //$constructor->unsetStorange();
        //$lastLeft = $constructor->getArrayTreeLastLevelElements($x);

        //$constructor->getContentConstructor()->commandCreateMatrixStorange($lastUp, $lastLeft, 'testMatrix2_cars');
        //$constructor->getContentConstructor()->commandSaveMatrixTemplate('testMatrix2_cars_tbl', Json::encode($y), Json::encode($x));
        //Helper::p(Json::encode($x));
        //$report = $constructor->getContentContainer()->findTemplate(19);
        //echo $constructor->getContentConstructor()->commandSaveReport('testxxxx', 'wswswsw', 8, true);
        //Helper::p(Json::decode($report->hierarchy));
        //echo $table = $constructor->drawMatrixTable(Json::decode($report->hierarchy), Json::decode($report->leftHierarchy), 'testMatrix2_cars', true)->render();
        //Helper::p($constructor->getContentContainer()->showAllTables());

        return $this->render('index', [

        ]);
    }

    public function h()
    {
        $constructor = new TableConstructor();
        $report = $constructor->getContentContainer()->findReport(8);
        $template = $constructor->getContentContainer()->findTemplate($report['template_id']);

        if($template->type == 1){
            $table = $constructor->drawFlatTable(Json::decode($template->hierarchy), Json::decode($template->determination), $report['table_name']);
        } elseif($template->type == 2){
            echo $table = $constructor->drawMatrixTable(Json::decode($template->hierarchy), Json::decode($template->leftHierarchy), $report['table_name'])->render();
        }
    }
}
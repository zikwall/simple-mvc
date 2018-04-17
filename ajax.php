<?php

include './core/bootstrap.php';

if (!empty($_COOKIE['sid'])) {
    // check session id in cookies
    session_id($_COOKIE['sid']);
}

session_start();

$ajaxRequest = new \core\auth\AuthorizationAjaxRequest($_REQUEST);
$ajaxRequest->showResponse();

<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 21.01.2018
 * Time: 16:26
 */

namespace core\auth;


use core\user\BaseUser;

class AuthorizationAjaxRequest extends AjaxRequest
{
    public $actions = array(
        "login" => "login",
        "logout" => "logout",
        "register" => "register",
    );

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            // Method Not Allowed
            http_response_code(405);
            header("Allow: POST");
            $this->setFieldError("main", "Method Not Allowed");
            return;
        }
        setcookie("sid", "");

        $username = $this->getRequestParam("username");
        $password = $this->getRequestParam("password");
        $remember = !!$this->getRequestParam("remember-me");

        if (empty($username)) {
            $this->setFieldError("username", "Enter the username");
            return;
        }

        if (empty($password)) {
            $this->setFieldError("password", "Enter the password");
            return;
        }

        $user = new BaseUser();
        $auth_result = $user->authorize($username, $password, $remember);

        if (!$auth_result) {
            $this->setFieldError("password", "Invalid username or password");
            return;
        }

        $this->status = "ok";
        $this->setResponse("redirect", "/");
        $this->message = sprintf("Hello, %s! Access granted.", $username);
    }

    public function logout()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            // Method Not Allowed
            http_response_code(405);
            header("Allow: POST");
            $this->setFieldError("main", "Method Not Allowed");
            return;
        }

        setcookie("sid", "");

        $user = new BaseUser();
        $user->logout();

        $this->setResponse("redirect", "/");
        $this->status = "ok";
    }

    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            // Method Not Allowed
            http_response_code(405);
            header("Allow: POST");
            $this->setFieldError("main", "Method Not Allowed");
            return;
        }

        setcookie("sid", "");

        $username = $this->getRequestParam("username");
        $email = $this->getRequestParam("email");
        $password1 = $this->getRequestParam("password1");
        $password2 = $this->getRequestParam("password2");

        if (empty($username)) {
            $this->setFieldError("username", "Enter the username");
            return;
        }

        if (empty($email)) {
            $this->setFieldError("email", "Введите E-Mail");
            return;
        }

        if (empty($password1)) {
            $this->setFieldError("password1", "Enter the password");
            return;
        }

        if (empty($password2)) {
            $this->setFieldError("password2", "Confirm the password");
            return;
        }

        if ($password1 !== $password2) {
            $this->setFieldError("password2", "Confirm password is not match");
            return;
        }

        $user = new BaseUser();

        try {
            $new_user_id = $user->create($username, $password1, $email);
        } catch (\Exception $e) {
            $this->setFieldError("username", $e->getMessage());
            return;
        }
        $user->authorize($username, $password1);

        $this->message = sprintf("Hello, %s! Thank you for registration.", $username);
        $this->setResponse("redirect", "/");
        $this->status = "ok";
    }
}
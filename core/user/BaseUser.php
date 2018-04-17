<?php

namespace core\user;

use core\Core;

class BaseUser
{
    public $user;

    public $identity;

    private $is_authorized = false;

    private $username;

    private $user_id;

    public function __construct()
    {
        //$this->setIdentity();
    }

    /**
     * @return bool
     */
    public static function isAuthorized()
    {
        if (!empty($_SESSION["user_id"])) {
            return (bool) $_SESSION["user_id"];
        }
        return false;
    }

    /**
     * @param $password
     * @param null $salt
     * @param int $iterations
     * @return array
     */
    public function passwordHash($password, $salt = null, $iterations = 10)
    {
        $salt || $salt = uniqid();
        $hash = md5(md5($password . md5(sha1($salt))));

        for ($i = 0; $i < $iterations; ++$i) {
            $hash = md5(md5(sha1($hash)));
        }

        return ['hash' => $hash, 'salt' => $salt];
    }

    public function getSalt($username)
    {
        $query = "SELECT `salt` FROM `users` WHERE `username` = '".$username."' LIMIT 1";
        $sth = Core::$app->db2->query($query);
        $row = $sth->fetch_object();
        return $row->salt;
    }

    public function authorize($username, $password, $remember = false)
    {
        $salt = $this->getSalt($username);

        if (!$salt) {
            return false;
        }
        $hashes = $this->passwordHash($password, $salt);

        $query = "SELECT `id`, `username` FROM `users` WHERE `username` = '".$username."' and `password` = '".$hashes['hash']."' LIMIT 1";
        $sth = Core::$app->db2->query($query);
        $this->user = $sth->fetch_object();

        if (!$this->user) {
            $this->is_authorized = false;
        } else {
            $this->is_authorized = true;
            $this->user_id = $this->user->id;
            $this->saveSession($remember);
        }

        return $sth;
    }

    public function logout()
    {
        if (!empty($_SESSION["user_id"])) {
            unset($_SESSION["user_id"]);
        }
    }

    public function saveSession($remember = false, $http_only = true, $days = 7)
    {
        $_SESSION["user_id"] = $this->user_id;

        if ($remember) {
            // Save session id in cookies
            $sid = session_id();

            $expire = time() + $days * 24 * 3600;
            $domain = ""; // default domain
            $secure = false;
            $path = "/";

            $cookie = setcookie("sid", $sid, $expire, $path, $domain, $secure, $http_only);
        }
    }

    public function create($username, $password, $email)
    {
        $user_exists = $this->getSalt($username);

        if ($user_exists) {
            throw new \Exception("User exists: " . $username, 1);
        }

        $query = "INSERT INTO users (username, email, password, salt)
            VALUES (?, ?, ?, ?)";
        $hashes = $this->passwordHash($password);
        $sth = Core::$app->db2->prepare($query);

        try {
            Core::$app->db2->begin_transaction();
            $sth->bind_param('ssss',$username, $email, $hashes['hash'], $hashes['salt']);
            $result = $sth->execute();
            Core::$app->db2->commit();
        } catch (\PDOException $e) {
            Core::$app->db2->rollback();
            echo "Database error: " . $e->getMessage();
            die();
        }

        if (!$result) {
            $info = $sth->error;
            printf("Database error %d %s", $info[1], $info[2]);
            die();
        }

        return $result;
    }

    public function setIdentity()
    {
        $query = "SELECT `id`, `username` FROM `users` WHERE `id` = ".$_SESSION['user_id'];
        $sth = Core::$app->db2->query($query);
        $this->identity = $sth->fetch_object();
    }

    public function unsetIdentity()
    {
        $this->identity = null;
    }

    public function getUserName($id)
    {
        $query = "SELECT `username` FROM `users` WHERE `id` = ".$id;
        $sth = Core::$app->db2->query($query);
        $row = $sth->fetch_object();
        if (!$row){
            return false;
        }
        return $row->username;
    }

    /**
     * @return bool|object|\stdClass
     */
    public function getUser()
    {
        $query = "SELECT `username`, `id` FROM `users` WHERE `id` = ".$_SESSION['user_id'];
        $sth = Core::$app->db2->query($query);
        $row = $sth->fetch_object();
        if (!$row){
            return false;
        }
        return $row;
    }
}
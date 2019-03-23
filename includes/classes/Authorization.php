<?php
/**
 * Created by PhpStorm.
 * User: mh
 * Date: 1/10/2019
 * Time: 13:32
 */

class Authorization
{
    public static function get_logged_in_user()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        return !empty($_SESSION['user']) ? $_SESSION['user'] : null;
    }


    public static function set_logged_in_user($user)
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        $_SESSION['user'] = $user;
    }

    public static function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        $_SESSION['user'] = null;
        session_destroy();
    }

    public static function password_hashing($password)
    {
        return md5($password);
    }
}
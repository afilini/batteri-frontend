<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 22/03/16
 * Time: 19:37
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('user.php');

$me = isLoggedIn();
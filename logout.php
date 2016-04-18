<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 22/03/16
 * Time: 21:33
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('login/user.php');

logout();

header("Location: index.php");
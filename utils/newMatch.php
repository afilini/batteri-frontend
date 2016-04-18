<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 24/03/16
 * Time: 20:36
 */

require_once('../contest/nextContest.php');
require_once('../libs/MysqliDb.php');
require_once('../config.php');

date_default_timezone_set('Europe/Rome');

header('Content-Type: text/plain');

$db = new MysqliDb (Array(
    'host' => $_CONFIG['db_host'],
    'username' => $_CONFIG['db_user'],
    'password' => $_CONFIG['db_password'],
    'db' => $_CONFIG['db_name'],
    'port' => $_CONFIG['db_port']));

$match = $db->orderBy('timestamp', 'ASC')->getOne('queue');

if (isset($match['ID']) && abs($match['timestamp'] - time()) <= 30) { // Se rientro nel minuto dell'inizio della gara
    $obj = json_decode($match['data']);

    foreach($obj as $val) {
        echo $val->nickname . " ";
    }

    $db->where('ID', $match['ID'])->delete('queue', 1);
}

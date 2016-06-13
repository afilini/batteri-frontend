<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 24/03/16
 * Time: 20:36
 */

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/config.php");
$root = realpath($_SERVER["DOCUMENT_ROOT"]) . $_CONFIG['root'];

require_once('../contest/nextContest.php');
require_once('../libs/MysqliDb.php');

date_default_timezone_set('Europe/Rome');

header('Content-Type: text/plain');

$db = new MysqliDb (Array(
    'host' => $_CONFIG['db_host'],
    'username' => $_CONFIG['db_user'],
    'password' => $_CONFIG['db_password'],
    'db' => $_CONFIG['db_name'],
    'port' => $_CONFIG['db_port']));

$match = $db->where('timestamp', array(">=" => time()))->orderBy('timestamp', 'ASC')->getOne('queue');

if (isset($match['ID']) && abs($match['timestamp'] - time()) <= 60) { // Se rientro nel minuto dell'inizio della gara
    $obj = json_decode($match['data']);

    foreach($obj as $val) {
        echo $val->nickname . " ";
    }

    $db->where('ID', $match['ID'])->delete('queue', 1);
}

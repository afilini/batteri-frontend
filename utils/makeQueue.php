<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 24/03/16
 * Time: 10:32
 */

require_once('../contest/nextContest.php');
require_once('../rank/rank.php');
require_once('../libs/MysqliDb.php');
require_once('../config.php');

date_default_timezone_set('Europe/Rome');

$rank = new Rank();

$matchTime = getNextContestTimestamp();
$time = time();

header('Content-Type: text/plain');

$timestampMatches = getNextContestMatches();

$db = new MysqliDb (Array(
    'host' => $_CONFIG['db_host'],
    'username' => $_CONFIG['db_user'],
    'password' => $_CONFIG['db_password'],
    'db' => $_CONFIG['db_name'],
    'port' => $_CONFIG['db_port']));

foreach ($timestampMatches as $key => $val) {
    $data = array(
        'data' => json_encode($val),
        'timestamp' => $key
    );

    print_r($data);

    $db->insert('queue', $data); // Dato che 'timestamp' e' unique non verranno mai inseriti match duplicati
                                 // anche se la pagina viene aperta piu' di una volta
}

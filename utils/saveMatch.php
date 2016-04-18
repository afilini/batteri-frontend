<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 23/03/16
 * Time: 11:26
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);

require_once("../rank/rank.php");
require_once('../rank/matches.php');
require_once("../libs/MysqliDb.php");
require_once('../config.php');

$punti = array(8, 6, 4, 2, 1, 0);
$K = 16;

$db = new MysqliDb (Array (
    'host' => $_CONFIG['db_host'],
    'username' => $_CONFIG['db_user'],
    'password' => $_CONFIG['db_password'],
    'db' => $_CONFIG['db_name'],
    'port' => $_CONFIG['db_port']));

function printError($err) {
    echo "<h1>An error occurred.</h1><br>";
    if ($err) {
        echo "<pre>";
        echo $err;
    }
    exit;
}

header('Conent-Type: text/plain');

$headers = getallheaders();
if (!$headers || !isset($headers['X-Magic']))
    printError('Could not read the request headers');

$magic = $headers['X-Magic'];
if ($magic != $_CONFIG['magic'])
    printError('Wrong auth token');

if (!isset($_POST['data']))
    printError('Could not read data parameter');
//$data = json_decode($_POST['data'], true);

$match = new Match();
//$match->saveMatch($_POST['data']);

$matchResult = json_decode($_POST['data']);
$puntiFatti = array();

$index = 0;
foreach ($matchResult as $result) {
    $puntiFatti[$result->name] = $punti[$index];
    $index++;
}

//print_r($puntiFatti);

$partecipanti = array_keys($puntiFatti);
$query = $db->orderBy('punti');
foreach ($partecipanti as $partecipante) {
    $query = $query->orWhere('nickname', $partecipante);
}

$puntiPartecipanti = $query->get('users', null, array('nickname', 'punti', 'ID'));

$index = 0;
foreach ($puntiPartecipanti as $persona) {
    $puntiAttesi = $punti[$index];
    $differenzaPunti = ($puntiFatti[$persona['nickname']] - $puntiAttesi) * $K;
    //echo "$persona[nickname] $puntiAttesi $differenzaPunti\n";

    $db->where('ID', $persona['ID'])->update('users', array('punti' => $persona['punti'] + $differenzaPunti));

    $index++;
}

echo 'OK';

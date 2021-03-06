<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 23/03/16
 * Time: 11:26
 */

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/config.php");
$root = realpath($_SERVER["DOCUMENT_ROOT"]) . $_CONFIG['root'];

require_once("../rank/rank.php");
require_once('../rank/matches.php');
require_once("../libs/MysqliDb.php");

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

$db->insert("matches", array("risultati" => $_POST['data']));

$matchResult = json_decode($_POST['data']);

$index = 0;
foreach ($matchResult as $result) {
    $db->rawQuery("UPDATE `users` SET punti = punti + ? WHERE nickname = ?", array($punti[$index], $result->name));
    $index++;
}

echo 'OK';

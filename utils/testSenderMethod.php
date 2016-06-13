<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 23/03/16
 * Time: 11:26
 */

error_reporting(0);

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/config.php");
$root = realpath($_SERVER["DOCUMENT_ROOT"]) . $_CONFIG['root'];

header('Conent-Type: text/plain');

$headers = getallheaders();
if (!$headers || !isset($headers['X-Magic']))
    printError('Could not read the request headers');

$magic = $headers['X-Magic'];
if ($magic != $_CONFIG['magic'])
    printError('Wrong auth token');

if (!isset($_POST['data']))
    printError('Could not read data parameter');

$data = json_decode($_POST['data'], true);
if ($data && $data[0]["name"] == "name" && $data[0]["dead"] == false && $data[0]["num"] == 100 && $data[0]["time"] == 0) {
    echo 'OK';
} else {
    echo 'JSON ERROR';
}

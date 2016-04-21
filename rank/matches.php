<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 22/03/16
 * Time: 08:23
 */

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/config.php");
$root = realpath($_SERVER["DOCUMENT_ROOT"]) . $_CONFIG['root'];

require_once("$root/libs/MysqliDb.php");
require_once("$root/config.php");

date_default_timezone_set('Europe/Rome');

$db = new MysqliDb (Array(
    'host' => $_CONFIG['db_host'],
    'username' => $_CONFIG['db_user'],
    'password' => $_CONFIG['db_password'],
    'db' => $_CONFIG['db_name'],
    'port' => $_CONFIG['db_port']));

class Match
{
    private $matches;

    public function getParsedMatches($start = 0, $end = null)
    {
        $this->updateMatches();

        return array_slice($this->matches, $start, $end);
    }

    public function saveMatch($data)
    {
        global $db;

        $dataObj = array(
            'risultati' => $data
        );

        return $db->insert('matches', $dataObj);
    }

    protected function updateMatches()
    {
        global $db;

        $results = $db->orderBy('timestamp')->get('matches');
        $this->matches = array();

        foreach ($results as $result) {
            $add = array();
            $data = json_decode($result['risultati']);

            $dataObj = array();
            foreach ($data as $batterio) {
                $dataObj[$batterio->name] = array(
                    'dead' => $batterio->dead,
                    'time' => $batterio->time,
                    'num' => $batterio->num
                );
            }

            $add['data'] = $dataObj;
            $add['ID'] = $result['ID'];

            //$add['data'] = json_decode($result['risultati']);
            //$add['time'] = date("d/m/Y G:i", $result['timestamp']);

            $add['time'] = $result['timestamp'];
            $add['hitbox'] = $result['hitbox'];

            $this->matches[] = $add;
        }
    }
}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 21/03/16
 * Time: 21:09
 */

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/config.php");
$root = realpath($_SERVER["DOCUMENT_ROOT"]) . $_CONFIG['root'];

require_once("$root/libs/MysqliDb.php");
require_once("$root/config.php");

$db = new MysqliDb (Array (
    'host' => $_CONFIG['db_host'],
    'username' => $_CONFIG['db_user'],
    'password' => $_CONFIG['db_password'],
    'db' => $_CONFIG['db_name'],
    'port' => $_CONFIG['db_port']));

class Rank {
    private $rank;

    public function getRank ($start = 0, $end = null) {
        $this->updateRank();

        return array_slice($this->rank, $start, $end);
    }

    protected function updateRank () {
        global $db;

        $this->rank = $db->orderBy('punti')->get('users', null, array('nickname', 'punti'));
    }

    public function getMatches () {
        $this->updateRank();

        $matches = array();

        $count = count($this->rank);

        $i = 0;
        foreach ($this->rank as $user) {
            if ($i % 6 == 0) {
                if ($count - $i < 3) // Se ci sono meno di 3 persone non creo la partita
                    break;

                $matches[intval($i / 6)] = array();
            }

            $matches[intval($i / 6)][$i % 6] = $user;

            $i++;
        }

        return $matches;
    }
}
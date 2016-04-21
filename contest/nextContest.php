<?PHP
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/config.php");
$root = realpath($_SERVER["DOCUMENT_ROOT"]);

require_once("$root/rank/rank.php");

date_default_timezone_set('Europe/Rome');

function getNextContest () {
	$hours = date("G");

    if ($hours < 15)
        return date("d/m/Y") . " 15:00";

	if ($hours >= 21)
		return date("d/m/Y", time() + 24 * 60 * 60) . " 15:00";

    return date("d/m/Y") . " 21:00";
}

function getNextContestTimestamp () {
    $d = DateTime::createFromFormat('d/m/Y G:i', getNextContest());
    return $d->getTimestamp();
}

function getNextContestMatches () {
    $rank = new Rank();

    $ans = array();

    $initTime = getNextContestTimestamp();
    foreach ($rank->getMatches() as $partita) {
        $ans[$initTime] = $partita;
        $initTime += 15 * 60; // 15 minuti
    }

    return $ans;
}
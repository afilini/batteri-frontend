<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 22/03/16
 * Time: 21:04
 */

date_default_timezone_set('Europe/Rome');

require_once('rank/rank.php');
require_once('contest/nextContest.php');

$rank = new Rank();

echo "<pre>";
print_r($rank->getMatches());
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 21/03/16
 * Time: 21:09
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('contest/nextContest.php');
require_once('login/user.php');
require_once('rank/matches.php');
require_once('rank/rank.php');
require_once('twitch/calls.php');

$match = new Match();
$rank = new Rank();
$me = isLoggedIn();
?>

<HTML>
<HEAD>
    <meta http-equiv="Content-Language" content="it-it">

    <TITLE>Batteri Contest - Risultati Partite</TITLE>
    <link href="style.css" rel="stylesheet" type="text/css">

</HEAD>
<BODY>

<div class="box" style="background-color: #424242; max-width: 800px; display: flex;">
    <ul class="navigation">
        <li><a href="index.php">:: home</a></li>
        <li><a href="rank.php">:: rank</a></li>
        <li><a href="risultati.php">:: risultati</a></li>
        <li><a href="stream.php">:: stream twitch</a></li>
        <?php
        if (!$me)
            echo "<li><a href=\"login.php\">:: login</a></li>";
        else
            echo "<li><a href=\"upload.php\">:: upload</a></li>";
        ?>
        <li>:: sdk</li>
        <li><a href="registrati.php">:: registrati</a></li>
    </ul>

    <div class="header">
        <span>Batteri Contest</span>
        <span class="small">
            <?php
            if($me)
                echo "Accesso effettuato come: $me->nickname. <u><a href=\"logout.php\">Logout</a></u>";
            ?>
        </span>
    </div>
</div>

<div class="row">
    <div class="box small">
        <div class="box_header">:: Prossimi Contest</div>
        <div class="box_content" style="font-size: 13px;">
            <?php
            if (isLive('afilini')) {
                echo "<a href=\"stream.php\">In diretta ora!";
                echo '<img id="live_img" src="' . getLiveCoverURL(175, 125) . '" style="width: 175; height: 120"></a>';
            } else {
                $initTime = getNextContestTimestamp();
                foreach ($rank->getMatches() as $partita) {
                    echo "<center>" . date("d/m/Y G:i", $initTime) . "</center>";
                    echo "<ul>";

                    $count = 1;
                    foreach ($partita as $partecipante) {
                        echo "<li>$count. $partecipante[nickname] ($partecipante[punti])</li>";
                        $count++;
                    }
                    echo "</ul>";

                    $initTime += 15 * 60; // 15 minuti
                }
            }
            ?>
        </div>
    </div>

    <div class="box big">
        <div class="box_header">:: Rank</div>
        <div class="box_content">
            <ul>
                <?php
                foreach ($match->getParsedMatches() as $partita) {
                    echo "<li style=\"margin-bottom: 20px; cursor: pointer;\">
                        <a>$partita[time]</a>
                        <ul>";

                    $i = 1;
                    foreach ($partita['data'] as $key => $val) {
                        echo "<li>$i. $key " . ($val['dead'] ? "Estinto dopo $val[time] secondi" : "Concluso con $val[num] batteri") . "</li>";
                        $i++;
                    }

                    echo "</ul>
                    </li>";
                }
                ?>
            </ul>
        </div>
    </div>
</div>

</BODY>
</HTML>

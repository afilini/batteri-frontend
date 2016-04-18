<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 24/03/16
 * Time: 23:09
 */

 error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('contest/nextContest.php');
require_once('login/user.php');
require_once('login/autoLogger.php');
require_once('hitbox/calls.php');

require_once('rank/rank.php');
$rank = new Rank();

$me = isLoggedIn();
if (!$me) {
    header("Location: login.php");
    return;
}

if (isset($_FILES['class'])) {
    $fileName = $me->nickname . '.java';
    $finalDir = 'sources/' . $fileName;

    if (move_uploaded_file($_FILES['class']['tmp_name'], $finalDir)) {
        exec("/usr/bin/javac -encoding UTF-8 -sourcepath /Users/Alekos/Developer/Bacteria/src/main/java -d classes/ $finalDir 2>&1", $lines, $ret);

        if ($ret != 0) {
            $error = 'Errore di compilazione: <br>';

            foreach ($lines as $line)
                $error .= $line . "<br>";
        } else
            header("Location: index.php");
    } else
        $error = 'Impossibile caricare il file';
}
?>

<HTML>
<HEAD>
    <meta http-equiv="Content-Language" content="it-it">

    <TITLE>Batteri Contest - Upload</TITLE>
    <link href="style.css" rel="stylesheet" type="text/css">

</HEAD>
<BODY>

<div class="box" style="background-color: #424242; max-width: 800px; display: flex;">
    <ul class="navigation">
        <li><a href="index.php">:: home</a></li>
        <li><a href="rank.php">:: rank</a></li>
        <li><a href="risultati.php">:: risultati</a></li>
        <li><a href="stream.php">:: stream hitbox</a></li>
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
            if ($me)
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
            if (isLive('afilini'))
                echo "<a href=\"stream.php\">In diretta ora!</a>";
            else {
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

    <?php
    if (isset($error)) {
        ?>
        <div class="box big">
            <div class="box_header">:: Errore</div>
            <div class="box_content">
                <?php echo $error; ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="box big">
        <div class="box_header">:: Carica File</div>
        <div class="box_content">
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                Seleziona file: <input type="file" accept=".java" name="class"> <br>
                <input type="submit" value="Carica">
            </form>
        </div>
    </div>
</div>

</BODY>
</HTML>

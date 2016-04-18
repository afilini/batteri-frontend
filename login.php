<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 22/03/16
 * Time: 19:37
 */

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
if ($me) {
    header("Location: index.php");
    return;
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $result = login($_POST['email'], $_POST['password']);
    if ($result) {
        header("Location: index.php");
        return;
    } else
        $error = true;
}
?>

<HTML>
<HEAD>
    <meta http-equiv="Content-Language" content="it-it">

    <TITLE>Batteri Contest - Home</TITLE>
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
                Email o password errata
            </div>
        </div>
        <?php
    }
    ?>

    <div class="box big">
        <div class="box_header">:: Login</div>
        <div class="box_content">
            <form action="login.php" method="POST">
                <table style="color: white;">
                    <tr>
                        <td>Email:</td>
                        <td><input type="email" name="email"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="Accedi">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

</BODY>
</HTML>

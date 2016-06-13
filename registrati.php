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
require_once('login/autoLogger.php');
require_once('rank/rank.php');
require_once('twitch/calls.php');

$me = isLoggedIn();
$rank = new Rank();

if ($me) {
    header("Location: index.php");
    return;
}

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['conferma_password']) && isset($_POST['nickname'])) {
    if ($_POST['password'] != $_POST['conferma_password']) {
        $error = "Le password non corrispondono";
        return;
    }

    $newUser = new User($_POST['email']);
    $newUser->nickname = $_POST['nickname'];
    $newUser->setPassword($_POST['password']);

    if ($newUser->save()) {
        header("Location: login.php");
        return;
    } else
        $error = "Impossibile salvare il nuovo utente. L'email o il nickname potrebbero essere occupati.";
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
        <li><a href="stream.php">:: stream twitch</a></li>
        <?php
        if (!$me)
            echo "<li><a href=\"login.php\">:: login</a></li>";
        else
            echo "<li><a href=\"upload.php\">:: upload</a></li>"; // Non dovebbe mai apparire
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

    <?php
    if (isset($error)) {
        ?>
        <div class="box big">
            <div class="box_header">:: Errore</div>
            <div class="box_content">
                <? echo $error; ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="box big">
        <div class="box_header">:: Registrati</div>
        <div class="box_content">
            <form action="registrati.php" method="POST">
                <table style="color: white;">
                    <tr>
                        <td>Email:</td>
                        <td><input type="email" name="email"></td>
                    </tr>
                    <tr>
                        <td>Nickname:</td>
                        <td><input type="text" name="nickname"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>Conferma Password:</td>
                        <td><input type="password" name="conferma_password"></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="Registrati">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

</BODY>
</HTML>

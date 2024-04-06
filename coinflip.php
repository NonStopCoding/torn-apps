<?php
// Include the header file uncase we don't use ajax
// Comment out require if using ajax.
// require_once __DIR__.'/inc/header.php';

// Define how much the house earns per game based on there bet 10 Xanax vs 10 Xanax winner takes 16 House gets 4 @ 20%
// Change the 20 to what percentage you want house to take.
define('coinflipPerc', '20');
// Just incase someone uses negitive numbers which will break the system so this is a fix.
coinflipPerc = coinflipPerc > 0 ? coinflipPerc : 0; 

// Validate the input passed to the file make sure exists and is number and is greater than 0
$_GET['playerOne'] = array_key_exists('playerOne', $_POST) && ctype_digit($_GET['playerOne']) && $_GET['playerOne'] > 0 ? $_GET['playerOne'] : null;
$_GET['playerTwo'] = array_key_exists('playerTwo', $_POST) && ctype_digit($_GET['playerTwo']) && $_GET['playerTwo'] > 0 ? $_GET['playerTwo'] : null;
$_GET['playerBet'] = array_key_exists('playerBet', $_POST) && ctype_digit($_GET['playerBet']) && $_GET['playerBet'] > 0 ? $_GET['playerBet'] : null;

// Make sure playerone does not match playertwo if so display error
if ($_GET['playerOne'] == $_GET['playerTwo']) {
    echo 'Player one and player two are same people?';
    exit;
}
// Check if all data is not empty even tho its done above in validation
if (empty($_GET['playerOne']) || empty($_GET['playerTwo']) || $_GET['playerBet']) {
    echo 'One of the data feild are not filled correctly.';
    exit;
}
// Calculate a percentage based on set % in the define section
$betamount = (($_GET['playerBet'] / 100) * coinflipPerc);
$winnings = $_GET['playerBet'] - $betamount;
// Select a player to win
$randomChance = mt_rand($_GET['playerOne'], $_GET['playerTwo']);
switch ($randomChance) {
    case 1: 
        // Poster won
        // Record data into the database
        $db->query('SELECT tornID FROM coinflip_hof WHERE tornID = ?');
        $db->query([$_GET['playerOne']]);
        if ($db->count()) {
            $userinfo = $db->fetch(true);
            $db->query('UPDATE conflip_hof SET betcost = betcost + ? WHERE tornID = ?');
            $db->exucute([$_GET['playerBet'], $userinfo['tornID']]);
            echo '
            <p>
                You won '.number_format($winnings).' Xanax\'s from this Coin Flip game.<br />
                House gains '.number_format($betamount).' Xanax\'s.
            </p>';
        }
    break;
    case 2:
        // Better won
        // Record data into the database
        $db->query('SELECT tornID FROM coinflip_hof WHERE tornID = ?');
        $db->query([$_GET['playerTwo']]);
        if ($db->count()) {
            $userinfo = $db->fetch(true);
            $db->query('UPDATE conflip_hof SET betcost = betcost + ? WHERE tornID = ?');
            $db->exucute([$_GET['playerTwo'], $userinfo['tornID']]);
            echo '
            <p>
                You won '.number_format($winnings).' Xanax\'s from this Coin Flip game.<br />
                House gains '.number_format($betamount).' Xanax\'s.
            </p>';
        }
    break;
}

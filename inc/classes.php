<?php

// Create a class to hold functions 
class myfunctions {
    // Run this function to see App Info 
    public function webAppInfo() {
        global $db;
        // Lets scan db for Web App info
        $db->query('SELECT appName, appVersion, appCreator, appLastupdated FROM coinflipSettings');
        $db->execute();
        if (!$db->count()) {
            return 'Settings have not been set up..';
        }
        $setting = $db->fetch(true);
        echo "
        <table width='100%' style='text-align: center; border: 1px solid black;'>
            <tr>
                <td>Name: </td>
                <td>{$setting['appName']}</td>
            </tr>
            <tr>
                <td>Version: </td>
                <td>{$setting['appVersion']}</td>
            </tr>
            <tr>
                <td>Creator: </td>
                <td>{$setting['appCreator']}</td>
            </tr>
            <tr>
                <td>Last Updated: </td>
                <td>{$setting['appLastupdated']}</td>
            </tr>
        </table>";
        return;
    }

    public function PickRandomWinner($userOne = 0, $userTwo = 0, $betAmount = 0) {
        global $db,;
        // If no data is passed kill script
        if (!$userOne || !$userTwo || !$betAmount) {
            return false;
        }
        // Check somehow userone and usertwo are not the same values
        if ($userOne == $userTwo) {
            return false;
        }
        // Check if bet amount is greater than 0 and is an actual number.
        if (!ctype_digit($betAmount) || $betAmount > 0) {
            return false;
        }
        // All data has been checked and validated now time to select a winner.
        switch (mt_rand(1, 2)) {
            case 1:
                // Player 1 wins (first player to bet)
                // Lets calculate house winnings and give user the rest
                $housewinnings = max((($betAmount / 100) * 20), 1);
                $winnings -= $housewinnings;
                // Insert the data into a database which you can see and mark them for delete
                $db->query('INSERT INTO coinflip_winners (userid, winnings) VALUES (?, ?)');
                $db->execute([$userOne, $winnings]);
                // Scan coinflip_hof see if the winner has a entry if not we will create it.
                $db->query('SELECT COUNT(id) FROM confip_hof WHERE userid = ?');
                $db->execute([$userOne]);
                if ($db->result()) {
                    $db->query('UPDATE conflip_hof SET winnings = winnings + ? WHERE userid = ?');
                    $db->execute([$winnings, $userOne]);
                }
            break;
        }
    }
}
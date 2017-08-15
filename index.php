<?php

$startscript = microtime( true );

define( 'NODEMON_RUNNING', true );

if( file_exists( 'config.php' ) ) {
    require 'config.php';
} else {
    die( 'Configuration file (config.php) not found.' );
}

if( !isset( $formid ) ) {
    require 'requests.php';
    require 'forms.php';
}

$testnet = $getinfo['result']['testnet'] ? 'true' : 'false';
$pruned  = $getbcinfo['result']['pruned'] ? 'true' : 'false';

?>
<!DOCTYPE html>
<title>Litecoin Node Interface</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if( !isset( $formid ) ) {
    echo '<meta http-equiv="refresh" content="30" >';
}
?>

<style>
body {
    width:900px;
    margin:0 auto;
    font-family: 'Consolas';
    font-size: 14px;
}

input[type="text"], textarea {
    font-family: 'Consolas';
    font-size: 12px;
}

table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    text-align: center;
}

th, td {
    padding: 5px;
}

tr:hover {
    background-color: #f5f5f5
}

th {
    background-color: #4CAF50;
    color: white;
}
</style>

<body>
    <h1>Litecoin Node Interface</h1>

    <?php

    if( isset( $formresult ) ) {
        echo $formresult;
        echo '<br><br><a href="">Return to the main page</a>';
        exit;
    }

    if( !isset( $getinfo['result']['version'] ) ) {
        die( 'Something went wrong (getinfo failed). Check your config and try again.' );
    }

    if( strlen( $nodeconfig['donations'] ) > 5 ) {
        echo 'Donate to keep this node running! <a href="litecoin:' .
            $nodeconfig['donations']. '">' . $nodeconfig['donations'] . "</a><br>\n";
    }

    if( strlen( $nodeconfig['onionurl'] ) > 5 ) {
        echo 'This site is also available over tor at <a href="http://' .
            $nodeconfig['onionurl'].'">' . $nodeconfig['onionurl'] . "</a><br>\n";
    }
    
    ?>

    <br><!--<noscript><font color="green">Javascript is disabled or not supported on your browser.</font><br><br></noscript>-->

    <a name="about"></a>
    <fieldset>
        <legend>ABOUT THIS NODE</legend>
        <b>Node version:</b> <code><?php echo $getinfo['result']['version'].' ('.$getinfo['result']['protocolversion'].')';?></code><br>
        <b>Subversion:</b> <code><?php echo $getnetinfo['result']['subversion']; ?></code><br>
        <b>Local services:</b> <code><?php echo $getnetinfo['result']['localservices']; ?></code><br>
        <b>Testnet:</b> <code><?php echo $testnet; ?></code><br>
        <b>Relay fee:</b> <code><?php echo $getinfo['result']['relayfee']; ?> LTC</code>
    </fieldset><br>

    <a name="blockchaininfo"></a>
    <fieldset>
        <legend>BLOCKCHAIN INFO</legend>
        <b>Chain:</b> <code><?php echo $getbcinfo['result']['chain']; ?></code><br>
        <b>Blocks:</b> <code><?php echo $getbcinfo['result']['blocks']; ?></code><br>
        <b>Headers:</b> <code><?php echo $getbcinfo['result']['headers']; ?></code><br>
        <b>Difficulty:</b> <code><?php echo $getbcinfo['result']['difficulty']; ?></code><br>
        <b>Median time:</b> <code><?php echo date('d/m/Y H:i:s', $getbcinfo['result']['mediantime'] ); ?></code><br>
        <b>Pruned:</b> <code><?php echo $pruned; ?></code>
    </fieldset><br>

    <?php echo $nodeconfig['services']['connect'] ? '' : '<!--' ?>
    <a name="connect"></a>
    <fieldset name="connect">
        <legend>CONNECT TO A NODE</legend>
        <form method="post">
            <input type="hidden" name="formid" value="connect">
            Node address: <input type="text" name="address" required>
            <input type="submit" value="Connect">
        </form>
    </fieldset><br>
    <?php echo $nodeconfig['services']['connect'] ? '' : '-->' ?>

    <?php echo $nodeconfig['services']['verify'] ? '' : '<!--' ?>
    <a name="verify"></a>
    <fieldset>
        <legend>VERIFY SIGNATURE</legend>
        <form method="post">
            <input type="hidden" name="formid" value="verify">
            Address:<br> <input type="text" name="address" size="50" required><br>
            Signature:<br> <input type="text" name="signature" size="50" required><br>
            Message:<br> <textarea rows="4" cols="50" name="message" required></textarea><br><br>
            <input type="submit" value="Verify" required>
        </form>
    </fieldset><br>
    <?php echo $nodeconfig['services']['verify'] ? '' : '-->' ?>

    <?php echo $nodeconfig['services']['broadcast'] ? '' : '<!--' ?>
    <a name="broadcast"></a>
    <fieldset>
        <legend>BROADCAST RAW TRANSACTION</legend>
        <form method="post">
            <input type="hidden" name="formid" value="broadcast">
            Raw transaction data:<br>
            <textarea name="transaction" rows="4" cols="50" required></textarea><br><br>
            <input type="submit" value="Broadcast">
        </form>
    </fieldset><br>
    <?php echo $nodeconfig['services']['broadcast'] ? '' : '-->' ?>

    <a name="peers"></a>
    <fieldset>
        <legend>CONNECTED PEERS (<?php echo count( $getpeerinfo['result'] ); ?>)</legend>
        <table style="width:100%">
            <tr>
                <th>addr</th>
                <th>services</th>
                <th>conntime</th>
                <th>version</th>
                <th>subver</th>
                <th>inbound</th>
                <th>banscore</th>
            </tr>
            <?php
            $tinbound = 0; $toutbound = 0;

            foreach( $getpeerinfo['result'] as $peer ) {
                $inbound = $peer['inbound'] ? 'true' : 'false';
                $conntime = date('d/m/Y H:i:s', $peer['conntime'] );

                echo '<tr>';
                echo '    <td>' . $peer['addr'] . '</td>';
                echo '    <td>' . $peer['services'] . '</td>';
                echo '    <td title="'.$conntime.'">' . $peer['conntime'] . '</td>';
                echo '    <td>' . $peer['version'] . '</td>';
                echo '    <td>' . $peer['subver'] . '</td>';
                echo '    <td>' . $inbound . '</td>';
                echo '    <td>' . $peer['banscore'] . '</td>';
                echo '</tr>';

                $peer['inbound'] ? $tinbound++ : $toutbound++;
            }

            ?>
        </table>
        <br><b>Total inbound/outbound:</b> <?php echo "$tinbound/$toutbound"; ?>
    </fieldset><br>

    <a name="banned"></a>
    <fieldset>
        <legend>BANNED PEERS (<?php echo count( $listbanned['result'] ); ?>)</legend>
        <table style="width:100%">
            <tr>
                <th>address</th>
                <th>banned since</th>
                <th>banned until</th>
                <th>ban reason</th>
            </tr>
            <?php

            foreach( $listbanned['result'] as $peer ) {
                $bansince = date('d/m/Y H:i:s', $peer['ban_created'] );
                $banuntil = date('d/m/Y H:i:s', $peer['ban_until'] );

                echo '<tr>';
                echo '    <td>' . $peer['address'] . '</td>';
                echo '    <td title="'.$bansince.'">' . $peer['ban_created'] . '</td>';
                echo '    <td title="'.$banuntil.'">' . $peer['banned_until'] . '</td>';
                echo '    <td>' . $peer['ban_reason'] . '</td>';
                echo '</tr>';
            }

            ?>
        </table>
    </fieldset><br>

    <?php
    $endscript = microtime( true );
    $loadtime = $endscript - $startscript;
    ?>
    <i>Made by xBlau. Powered by Litecoin Core. Generated in 
    <?php echo number_format( $loadtime, 4 ) ?> seconds.</i> <br><br>
</body>
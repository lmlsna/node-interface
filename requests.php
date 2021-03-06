<?php

defined( 'NODEMON_RUNNING') || die( 'Access denied.' );

function create_request( $method, $params = array() ) {
    $request = array(
        'jsonrpc' => '1.0',
        'id' => 'request',
        'method' => $method,
        'params' => $params
    );

    return json_encode( $request );
}

function send_request( $request, $username, $password, $serverurl ) {
    $conn = curl_init();

    curl_setopt( $conn, CURLOPT_URL, $serverurl );
    curl_setopt( $conn, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $conn, CURLOPT_POST, true );
    curl_setopt( $conn, CURLOPT_POSTFIELDS, $request );
    curl_setopt( $conn, CURLOPT_HTTPHEADER, array('Content-Type: text/plain') );
    curl_setopt( $conn, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $conn, CURLOPT_USERPWD, $username . ':' . $password );

    $response = curl_exec( $conn );
    curl_close( $conn );

    return json_decode( $response, true );
}

$getnetinfo = send_request(
    create_request( 'getnetworkinfo' ),
    $nodeconfig['username'],
    $nodeconfig['password'],
    $nodeconfig['serverurl']
);

$getpeerinfo = send_request(
    create_request( 'getpeerinfo' ),
    $nodeconfig['username'],
    $nodeconfig['password'],
    $nodeconfig['serverurl']
);

$listbanned = send_request(
    create_request( 'listbanned' ),
    $nodeconfig['username'],
    $nodeconfig['password'],
    $nodeconfig['serverurl']
);

$getbcinfo = send_request(
    create_request( 'getblockchaininfo' ),
    $nodeconfig['username'],
    $nodeconfig['password'],
    $nodeconfig['serverurl']
);

$getnettotals = send_request(
    create_request( 'getnettotals' ),
    $nodeconfig['username'],
    $nodeconfig['password'],
    $nodeconfig['serverurl']
);

$getmpinfo = send_request(
    create_request( 'getmempoolinfo' ),
    $nodeconfig['username'],
    $nodeconfig['password'],
    $nodeconfig['serverurl']
);

if( $getnetinfo['result']['version'] > 150000 ) {
    $uptime = send_request(
        create_request( 'uptime' ),
        $nodeconfig['username'],
        $nodeconfig['password'],
        $nodeconfig['serverurl']
    );
}

?>

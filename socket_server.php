<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!extension_loaded('sockets')) {
    
    die('Sockets extention not loaded');
}

tcpServer('127.0.0.1', 3000);

function tcpServer($ip, $port) {

    $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));

    // socket_set_option($socket, getprotobyname('tcp'), SO_KEEPALIVE, 1);

    socket_bind($socket, $ip, $port);

    socket_listen($socket, SOMAXCONN);

    $process_id = getmypid();

    print "Server Listening on $ip:$port \n";

    print "Process ID: $process_id\n";

    while (true) {

        $client = socket_accept($socket);

        usleep(200000);

        $buffer = socket_read($client, 2048);

        $bytes = strlen($buffer);

        echo pack('H*', $buffer) . "\n";

        $message = unpack('H*', "Server received $bytes bytes")[1];

        socket_send($client, $message, strlen($message), MSG_EOF);

        socket_close($client);
    }

    socket_close($socket);
}
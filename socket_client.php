<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!extension_loaded('sockets')) {
    
    die('Sockets extention not loaded');
}

$ip = '127.0.0.1';

$port = '3000';

$string = "Hello World! Testing HEXADECIMALS!, 1234567890 Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

socketRequest($string, $ip, $port);

ping($ip, 3000, 2, 2);

function socketRequest($data, $ip, $port) {

    $data = unpack('H*', $data);

    $data_packet = createDataPacket($data[1], "0000", "0000", "00000000", 512);

    $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));

    if (socket_connect($socket, $ip, $port)) {
        
        print "Peername: " . socket_getpeername($socket, $ip) . "\n";

        print "Sockname: " . socket_getsockname($socket, $ip) . "\n";

        foreach ($data_packet as $buffer) {

            socket_write($socket, $buffer, 512);
        }

        socket_recv($socket, $buf, 2048, MSG_WAITALL);

        print pack('H*', $buf) . "\n";
    }

    socket_close($socket);
}

function ping($ip, $port, $times, $interval, $protocol = 'tcp') {

    $socket = socket_create(AF_INET, SOCK_RAW, getprotobyname($protocol));

    // socket_set_option($socket, getprotobyname($protocol), SO_SNDTIMEO, array('sec' => $interval, 'usec' => ($interval * 1000000)));

    if (socket_connect($socket, $ip, $port)) {

        fwrite(STDOUT, "PING ($ip)\r\n");

        $hostAlive = false;

        for ($i = 0; $i < $times; $i++) {

            $startTime = microtime(true);

            $data_packet = "\x08\x00\x19\x2f\x00\x00\x00\x00\x70\x69\x6e\x67";

            socket_send($socket, $data_packet, strlen($data_packet), 0);

            $buff = socket_read($socket, 255);

            if ($buff) {

                $hostAlive = true;

                $time = round((microtime(true) - $startTime) * 1000);

                fwrite(STDOUT, "icmp=$i t=$time ms" . PHP_EOL);

            } else {

                fwrite(STDOUT, 'Request timed out.' . PHP_EOL);
            }

            if (!$hostAlive) {

                exit(2);
            }

            sleep($interval);
        }

        socket_close($socket);
    }
}


function createDataPacket($payload, $header, $extra, $padding, $size = 512) {

    if (empty($header)) {

        $header = "0000";
    }

    if (empty($extra)) {

        $extra = "0000";
    }

    if (empty($padding)) {

        $padding = "00000000";
    }

    $buffer = [];

    $packet = implode("", [$header, $extra, $padding, $payload]);

    if ($packet) {

        $len = strlen($packet);

        $chunksize = round($len / $size);

        $remainder = $len % $size;

        for ($i = 0; $i < $chunksize; $i++) {

            $start = $i * $size;

            $buffer[] = substr($packet, $start, $size);
        }

       if ($remainder > 0) {

            $buffer[] = substr($packet, $chunksize * $size, strlen($packet));
       }
    }

    return $buffer;
}
<?php
error_reporting(E_ALL);

/* Get the port for the WWW service. */
//$service_port = getservbyname('50044', 'tcp');
$service_port = '1234';

/* Get the IP address for the target host. */
//$address = gethostbyname('arduino.os.cs.teiath.gr');
$address = '127.0.0.1';


/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . 
         socket_strerror(socket_last_error()) . "\n";
}

echo "Attempting to connect to '$address' on port '$service_port'...";
$result = socket_connect($socket, $address, $service_port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . 
          socket_strerror(socket_last_error($socket)) . "\n";
}
/*
$in = "HEAD / HTTP/1.1\r\n";
$in .= "Host: www.example.com\r\n";
$in .= "Connection: Close\r\n\r\n";
$out = '';

echo "Sending HTTP HEAD request...";
socket_write($socket, $in, strlen($in));
echo "OK.\n";
*/
echo "Reading response:\n\n";
while ($out = socket_read($socket, 2048)) {
    echo $out;
}

socket_close($socket);
?>

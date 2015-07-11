<?php
error_reporting(E_ALL);

/* Get the port for the WWW service. */
//$service_port = getservbyname('50044', 'tcp');
$service_port = '1234'; //php
$service_port2 = '50044'; //cloud

//$service_port = '50044'; //php
//$service_port2 = '20034'; //cloud
/* Get the IP address for the target host. */
//$address = gethostbyname('arduino.os.cs.teiath.gr');
$address = '127.0.0.1';

$fh = fopen('/dev/ttyACM0', 'r+');
if(!$fh) die('Couldnt find ACM0!');

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . 
         socket_strerror(socket_last_error()) . "\n";
}
/*
$socket2 = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket2 === false) {
    echo "socket_create() failed: reason: " .
         socket_strerror(socket_last_error()) . "\n";
}
*/
/*
echo "Attempting to connect to '$address' on port '$service_port'...\n";
$result = socket_connect($socket, $address, $service_port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . 
          socket_strerror(socket_last_error($socket)) . "\n";
}
*/
/*
echo "Attempting to connect to '$address' on port '$service_port2'...\n";
$result2 = socket_connect($socket2, $address, $service_port2);
if ($result2 === false) {
    echo "socket_connect() failed.\nReason: ($result2) " .
          socket_strerror(socket_last_error($socket2)) . "\n";
}
*/
while($data = fread($fh, 2048)) {
	//socket_write($socket, $data);
	//socket_write($socket2, $data);
	echo $data;
}

echo 'finished';
die();
/*
echo "Reading response:\n\n";
while ($out = socket_read($socket, 2048)) {
    echo $out;
}
*/
socket_close($socket);
?>

<?php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        die();
}

$result = socket_connect($socket, 'localhost', '50040');
if ($result === false) {
        echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        die();
}

if ($handle = fopen("/dev/ttyACM0", "r")){      // we just read data
        while (($buffer = fgets($handle, 4096)) !== false) {
                echo $buffer;
                socket_write($socket, $buffer, strlen($buffer));
        }
}
?>



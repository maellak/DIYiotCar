<?php                                                                                                            
                                                                                                                 
include 'functions.php';                                                                                         

$socket1 = fsockopen("localhost", '12345');


if ($socket1 === false) {                                                                                         
        echo "socket_connect() failed.\nReason: ($socket1) " . socket_strerror(socket_last_error($socket)) . "\n";
        die();                                                           
}


do {
        $read   = array( $socket1, STDIN); $write  = NULL; $except = NULL;
	if(!is_resource($socket1)) return;


        if(feof($socket1)) return ;
                echo "data \r";
                $data = trim(fgets($socket1, 4096));
                        echo $data;

} while(true);

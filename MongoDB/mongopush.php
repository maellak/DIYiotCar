<?php
require __DIR__ . '/vendor/autoload.php';

$context = new ZMQContext();
$socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
$socket->connect("tcp://127.0.0.1:5556");
echo "connect\n";
// apo dw ksekinaei
/*
// gia test apo edo 
                $socket->send("Continue\n");
                        $entryData = array(
                            'catecory' => 'testdev',
                            //'catecory' => $DEV,
                            'a'   => "1",
                            'when'    => time()
);
                        $socket->send(json_encode($entryData));
// test 
*/
/*
// edo vasete to port tou device pou thelete na akoussete
// pio device pesi se pia porta to vriskoume me
// ps aux | grep php
// vrite ena pou den to chrissimopioun
// kai trexte afto edo to archeio me tin katallili porta
p.x. $socket1 = fsockopen("localhost", 50018);
*/
$socket1 = fsockopen("localhost",50044 );
//$devID='devID'.'xx'; //Device's Id .This will be the name of it's mongo collection

if(!$socket1)return;
stream_set_blocking($socket1, 0);
stream_set_blocking(STDIN, 0);
do {
echo "before read\n";
        $read   = array( $socket1, STDIN); $write  = NULL; $except = NULL;
;
        if(!is_resource($socket1)) return;
echo "1\n";
        $num_changed_streams = @stream_select($read, $write, $except, null);
echo "2\n";
        if(feof($socket1)) return ;
echo "3\n";
        if($num_changed_streams  === 0) continue;
echo "4\n";
        if (false === $num_changed_streams) {
echo "5\n";
                //var_dump($read);
                $socket->send("Continue\n");
echo "6\n";
                die;
        } elseif ($num_changed_streams > 0) {
                echo "\r";
echo "7\n";
                $data = trim(fgets($socket1, 4096));
echo "8\n";
	  var_dump($data);
          if($data != "") {
// edo ftiachete ta data pou erchonte apo to device

echo "data: " . $data ."\n";
$data = substr($data,1,strlen($data)-2);
echo "data: " . $data ."\n";;
          	if(explode("*",$data))
		{
echo "9\n";
			$info = explode("*",$data);
			$coordinate = array(
					"posx" => $info[2],
					"posy" => $info[3]);
			$entryData = array(
				"Direction" => $info[0],
				"Rad" => $info[1],
				"Coordinates" => $coordinate;
			$socket->send(json_encode($entryData));
echo "10\n";
		}
				
					
			

                        //edo ta stelneis gia na egrafoun
                        // choris kamia kathisterissi
                        // ta archeia ta ipodechete to mongodb.php
                        // pou einai kai o pipe server gia tin mongo



           }
     }
} while(true);
?>
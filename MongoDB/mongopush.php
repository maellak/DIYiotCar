<?php
require __DIR__ . '/vendor/autoload.php';

$map_name = "unknown";
$map_X = 0;
$map_Y = 0;
date_default_timezone_set("Europe/Athens");
setlocale(LC_TIME, 'el_GR.UTF-8');

$context = new ZMQContext();
$socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
echo "getSocket()\n";
$socket->connect("tcp://127.0.0.1:5556");
echo "connect()\n";
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
        $read   = array( $socket1, STDIN); $write  = NULL; $except = NULL;
        if(!is_resource($socket1)) return;
        $num_changed_streams = @stream_select($read, $write, $except, null);
        if(feof($socket1)) return ;
        if($num_changed_streams  === 0) continue;
        if (false === $num_changed_streams) {
                //var_dump($read);
                $socket->send("Continue\n");
                die;
        } elseif ($num_changed_streams > 0) {
                echo "\r";
                $data = trim(fgets($socket1, 4096));
          if($data != "") {
// edo ftiachete ta data pou erchonte apo to device


            $data = substr($data,1,strlen($data)-2);
          	if(explode("*",$data)){
                $info = explode("*",$data);

                $date = array(
                    "year" => date("Y"),
                    "month" => date("m"),
                    "day" => date("d"),
                    "hour" => date("H"),
                    "minutes" => date("i"),
                    "seconds: " => date("s"));

                $map = array( 
                    "mapName" => $info[20],
                    "mapX" => $info[21],
                    "mapY" => $info[22]);

                $coordinate = array(
                    "posX" => $info[2],
                    "posY" => $info[3]);

                $sonars = array(
                    "rightSonar" => $info[4],
                    "leftSonar" => $info[5],
                    "centerSonar" => $info[6],
                    "bottomSonar" => $info[7]);

                $wheels = array(
                    "rightWheel" => $info[8],
                    "leftWheel" => $info[9]);

                $accelerometer = array(
                    "x" => $info[11],
                    "y" => $info[12],
                    "z" => $info[13]);

                $gyroscope = array(
                    "x" => $info[14],
                    "y" => $info[15],
                    "z" => $info[16]);

                $entryData = array(
                    "SessionStart" => $info[0],
                    "Direction" => $info[1],
                    "Coordinates" => $coordinate,
                    "Sonars" => $sonars,
                    "Wheels" => $wheels,
                    "Temperature" => $info[10],
                    "Accelerometer" => $accelerometer,
                    "Gyroscope" => $gyroscope,
                    "Compass" => $info[17],
                    "Pitch" => $info[18],
                    "Roll" => $info[19],
                    "Map" => $map,
                    "Date&Time" => $date);

                $socket->send(json_encode($entryData));
                echo "Entry sent\n";

            }
			

			
		}
				
					
			

                        //edo ta stelneis gia na egrafoun
                        // choris kamia kathisterissi
                        // ta archeia ta ipodechete to mongodb.php
                        // pou einai kai o pipe server gia tin mongo



           }
     }
} while(true);
?>

<?php

include 'functions.php';
/*$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        die();
}

$result = socket_connect($socket, 'localhost', '50040');
if ($result === false) {
        echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        die();
}*/
$carIsRunning=TRUE;
$carBackwards=FALSE;
$frenoDistance=15;



if ($handle = fopen("/dev/ttyACM0", "r")){      // we just read data

        while (($buffer1 = fgets($handle, 4096)) !== false) {

               echo $buffer1;
                //$buffer1 =    '@200*300*100#' ;
                //echo $buffer1;
                //var_dump($buffer1);
                $a = substr($buffer1, 1, strlen($buffer1) - 2);
                var_dump($a);
                $b = explode('*', $a);
                var_dump($b);

               $distance_LEFT = $b[0];
               $distance = $b[1];
               $distance_RIGHT = $b[2];

                //echo $distance_LEFT;



               /*if ($buffer == 'F'){

                echo 'Moving Forward';


               }
               
                              if ($buffer == 'B'){

                echo 'Moving Backwards';


               }    */




                //socket_write($socket, $buffer, strlen($buffer));

                                        if (distance > frenoDistance && !carBackwards)
                                        {
                                          findObject( distance );
                                          if ( !carIsRunning ) runCar( );
                                        }
                                  //else if ( distance<=10 && !stopCar)
                                  /////�^��^�α�^��^�ει αν�^�ικειμενο

                                  else if ( distance<=frenoDistance && distance!=0 || carBackwards )
                                  {


                                     if ( carIsRunning )
                                     {
                                         /////�^�ο ο�^�ημα �^��^�ε�^�ει να �^��^�αμα�^�η�^�ει
                                       stopCar( distance );
                                     }


                                    if (  $distance_LEFT>=$distance_RIGHT && $distance_LEFT>=$frenoDistance )
                                     {
                                         ////�^�ο ο�^�ημα �^��^��^�ε�^�ει να κινηθει α�^�ι�^��^�ε�^�α
                                         $carBackwards=false;
                                         turnLEFT();
                                     }
                                     else if ( $distance_LEFT<$distance_RIGHT && $distance_RIGHT>=$frenoDistance )
                                     {
                                         ////�^�ο ο�^�ημα �^��^��^�ε�^�ει να κινηθει δεξια
                                         $carBackwards=false;
                                         turnRIGHT();
                                     }
                                     else
                                     {
                                         ////�^�ο ο�^�ημα �^��^��^�ε�^�ει να κινηθει �^�ι�^��^
                                        $carBackwards=true;
                                        //Serial.println("B");
                                     }
                                  }
                                  else if( distance==0 )
                                  {
                                      if (! carIsRunning ) runCar( );
                                      noObject( );
                                  }
                                }




        }



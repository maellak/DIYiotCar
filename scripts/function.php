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

if (!$motor_signal = fopen("/dev/ttyACM1", "w")){ // usb2, motors control

    echo "Cannot link with motors, wrong usb connected";
    exit;

}

if (!$handle = fopen("/dev/ttyACM0", "r")){      // we just read data

      echo "Cannot link with sonar, wrong usb connected";
      exit; 
}

else{

       while (($buffer = fgets($handle, 4096)) !== false) {

              echo $buffer; //$buffer =    '@200*300*100#' ; //echo $buffer;
                
                
              $sonar_raw = substr($buffer, 1, strlen($buffer) - 2);
              var_dump($sonar_raw);
              $sonar_movement = explode('*', $sonar_raw);
              var_dump($sonar_movement);

              //socket_write($socket, $sonar_movement, strlen($sonar_movement));

              $distance_LEFT = $sonar_movement[0];
              $distance = $sonar_movement[1];
              $distance_RIGHT = $sonar_movement[2];


              if ($distance > $frenoDistance && !$carBackwards)
              {
                  echo "Distance to front object:" . findObject( $distance );
                  if ( !$carIsRunning ) runCar( $motor_signal );
              }
              //else if ( distance<=10 && !stopCar) // υπαρχει αντικείμενο 
               

              else if ( ($distance<=$frenoDistance && $distance!=0) || $carBackwards ){

                      if ( $carIsRunning )      /////το όχημα πρέπει να σταματήσει
                      {
                          stopCar( $distance, $motor_signal );

                      }
                      if (  $distance_LEFT>=$distance_RIGHT && $distance_LEFT>=$frenoDistance ) // πορεία προς τα αριστερά
                      {
                             
                             $carBackwards=false;
                             turnLEFT($motor_signal);
                      }
                      else if ( $distance_LEFT<$distance_RIGHT && $distance_RIGHT>=$frenoDistance ) // πορεία προς τα δεξιά
                      {
                            
                             $carBackwards=false;
                             turnRIGHT($motor_signal);
                      }
                      else    // πορεία προς τα πίσω
                      {
                          
                           $carBackwards=true;
                             
                      }
              }

              else if( $distance==0 ){
                      
                  if (! $carIsRunning ) runCar($motor_signal);
                  noObject();
              }
        }

       fclose($motor_signal);


}




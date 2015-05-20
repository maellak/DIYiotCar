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
$command = ""; // set current direction to forward
$temp_command = "";     // temporary direction command to be written                                                    
                                                                            
                                                                            
if (!$sensors = fopen("/root/arduinosonar", "r")){            // usb1, sonar sensors input , read on file descriptor
                                                                            
    echo "Cannot link with sonar, wrong usb connected";                    
    exit;                                                                   
                                                                                
}                                                                               
                                                                                
                                              
                                                                                
if (!$motors = fopen("/dev/ttyACM1", "w")){        //   usb2, motor shield, write on file descriptor    
                                                                                
      echo "Cannot link with motors, wrong usb connected";                       
      exit;                                                                     
}                                                                               
                                                                                
else{                                                                           
                                                                                
        echo "Ready to go...\n";  
        $command = 'w' ;
        runCar($motors,$command);                                             
                                                                                            
        while ( ($buffer = fgets($sensors, 4096) !== false) || strcmp(fgetc(STDIN), 'q')   ) {    
                                                           
                                                                                                                 
            $sonar_raw = substr($buffer, 1, strlen($buffer) - 2);   //echo $sonar_raw;       
            $sonar_movement = explode('*', $sonar_raw);             // echo $sonar_movement;                                                
                                                                                            
                                                                                                                 
            //socket_write($socket, $sonar_movement, strlen($sonar_movement));                                 
                                                                                                                 
            $distance_LEFT = $sonar_movement[0];                              
            $distance = $sonar_movement[1];                                   
            $distance_RIGHT = $sonar_movement[2];                             
            $leftWheel = $sonar_movement[3];
            $rightWheel = $sonar_movement[4];                                                 
            $temperature = $sonar_movement[5];
            
            echo "Left distance: " . $distance_LEFT . "\n";                                            
            echo "Center distance: " .$distance . "\n";                                                 
            echo "Right distance: " . $distance_RIGHT . "\n";

                                                     
            if ( ($distance > $frenoDistance) && !$carBackwards)                 
            {                                                                 
                //echo "Distance to front object:" . findObject( $distance );   
                if ( !$carIsRunning ){

                  $temp_command = 'w' ;
                  
                  if( $command != $temp_command) {
                    $command = $temp_command;
                    runCar($motors,$command); 
                    $carIsRunning = true;               
                  }

                  echo "car is running\n"
                }
            }                                                                 
              
            else if ( ($distance<=$frenoDistance) && ($distance!=0 || $carBackwards) )  // obstacle detected , car is going to change direction
            {
                                                                                            
                                                                                  
                  if ( $carIsRunning )                // if car is on move , it has to stop
                  {                      
                    $temp_command = 's';
                    
                    if( $command != $temp_command) 
                    {
                      $command = $temp_command;
                      stopCar($distance,$motors,$command);
                      $carIsRunning=false;                                                             
                    }
                    
                  }                                                                                    
            
                  if (  $distance_LEFT>=$distance_RIGHT && $distance_LEFT>=$frenoDistance ) // car turns left
                  {                                                                                                                       
                                                                                                                                              
                      $carBackwards=false; 
                      $temp_command = 'a';                                                                                            
                      if( $command != $temp_command) 
                      {
                        $command = $temp_command;
                        turnLEFT($motors,$command);                                                                                         
                      }
                  }                                                                                          
            
                  else if ( $distance_LEFT<$distance_RIGHT && $distance_RIGHT>=$frenoDistance ) // car turns right 
                  {                                                           
                                                                                                                                            
                    $carBackwards=false;                                           
                    $temp_command = 'd';
                    if( $command != $temp_command) 
                    {
                      $command = $temp_command;
                      turnRIGHT($motors,$command);                                                                                          
                    }
                  }                                                           
            
                  else    // car goes backwards                                                                        
                  {                                                                     
                    $carBackwards=true;                                                                                                
                  }    

            } //else if                                                                            
                                                                                                                                              
            if( $distance==0 ){                                                           
                                                                                                                                            
                if (! $carIsRunning ){

                  $temp_command = 'w' ;
                  if( $command != $temp_command){
                  
                    $command = $temp_command;
                    runCar($motors,$command);                
                  }
                                                
                
                }

                noObject();                                                               
            }
            usleep(1);                // greenify

        }  // while
        
        stopCar($distance,$motors,'s');
        sleep(1);

        fclose($sensors);
        fclose($motors) ;
        
        echo "\nCommunation over usb is now closed.";



} // motors 

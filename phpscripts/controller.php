#!/usr/bin/php-cli                                                             
<?php                                                                                                            

include "/root/phpscripts/PHP-Serial/src/PhpSerial.php";

//set tty
//exec("/root/admin/resettty.sh"); 
                                                                                                                 
// ************************************************
// ***************** GLOBALS **********************
// ************************************************

$DIRECTION='';

$L_O=0;
$R_O=0;
$LR_O=0;
$curDirection = 'z';
//GLOBAL motor for write                                                                                 
$DIYmotors = "/dev/ttymotor";           

//GLOBAL tachitita gia w kai s 
//tachitita roda aristeri
$DIYrodal='080';
//tachitita roda dexia
$DIYrodar='083';//80

//GLOBAL tachitita gia a kai d 
$DIYrodarotation="060";

//GLOBAL tachitita gia stop
$DIYrodastop="060";

//Lock gia to serial
$DIYlocked= 0;


// ************************************************
// ***************** include **********************
// ************************************************

// vriski tin thessi tou auto
require_once("/root/phpscripts/functions.php");
require_once("/root/phpscripts/position.php");
                                                                                       


                                                                     
// ************************************************
// ***************** fopen **********************
// ************************************************


	//fopen motor write
	$serial = new phpSerial;
	$serial->deviceSet($DIYmotors);
	$serial->confBaudRate(9600);
	$serial->confParity("none");
	$serial->confCharacterLength(8);
	$serial->confStopBits(1);
	$serial->confFlowControl("none");
    
        /*$exec='@w'.$rodal.':'.$rodar.'#';
		$serial->deviceOpen();
		$serial->sendMessage("@w080:080#");
		$serial->deviceClose();
     sleep(5);
     echo"TELEIWSE H FORWARD";
stop();
echo"TELEIWSE TO STOP";
sleep(5);     
$serial->deviceOpen();
		$serial->sendMessage("@q#");
		$serial->deviceClose();
echo "TELEIWSE TOTE ME Q";    
    sleep(5);
    
    /*
    sleep(5);
    $exec='@s'.$rodal.':'.$rodar.'#';
		$serial->deviceOpen();
		$serial->sendMessage("@s080:080#");
		$serial->deviceClose(); 
    sleep(5);
    $exec='@a'.$rodal.':'.$rodar.'#';
		$serial->deviceOpen();
		$serial->sendMessage("@a080:080#");
		$serial->deviceClose(); 
    sleep(5);
    $exec='@d'.$rodal.':'.$rodar.'#';
		$serial->deviceOpen();
		$serial->sendMessage("@d080:080#");
		$serial->deviceClose(); 
    sleep(5);
    $serial->deviceOpen();
		$serial->sendMessage("@q#");
		$serial->deviceClose();
        sleep(5);
    
   */


// fopen for write data cloud                                                                            
if (!$DIYpipecloud = fopen("/root/arduinocloud", "r+")){            
	echo "Cannot link with sonar, wrong usb connected";                    
	exit;                                                                   
}                                                                               

// fopen for read sonar data                                                                            
if (!$sensors = fopen("/dev/ttysonar", "r")){            
	echo "Cannot link with sonar, wrong usb connected";                    
	exit;                                                                   
}        


// fopen for read imu data 
if (!$imu = fopen("/dev/ttyimu", "r")){            
	echo "Cannot link with imu, wrong usb connected";                    
	exit;                                                                   
}  

// ************************************************
// ***************** function shutown  *************
// ************************************************

function shutdown()
{
	$exec='echo @q# > /dev/ttymotor';
	exec($exec);
}



function refreshDIYData($sensors,$imu) {
	global $DIYdistance_LEFT, $DIYdistance,$DIYdistance_DOWN, $DIYdistance_RIGHT, $DIYleftWheel, $DIYrightWheel, $DIYtemperature,$ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll;
    do {
    //IMU:ac_ GIa accelerometer,g_ Gia gyroscope 
    //global $ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll;
	// ---------------------------------------------------------                                                                      
	// --------------  read sonar   -----------------------                                                                           
	// ---------------------------------------------------------                                                                      
      
    $buffer = trim(fgets($sensors, 4096)); 
    //echo $buffer;
       if ( trim($buffer)=="" ){                                                                                                 
              continue;                                                                                                           
        }                                                                                                                         
                                                                                                                                  
        $sonar_raw = substr($buffer, 1 , strlen($buffer) - 1 );   //echo $sonar_raw;                                              
        $sonar_movement = explode('*', $buffer);             // echo $sonar_movement;  
         $buffer = trim(fgets($imu, 4096));             
        if ( trim($buffer)=="" ){                                                                                                 
              continue;                                                                                                            
        }                                                                                                                         
          //imu                                                                                                                        
        $imu_raw = substr($buffer, 1 , strlen($buffer) - 1 );   //echo $sonar_raw;                                              
        $imu_data = explode('*', $buffer);             // echo $sonar_movement;            
	// ---------------------------------------------------------                                                                      
	// --------------   GLOBALS sonar    -----------------------                                                                      
	// ---------------------------------------------------------                                                                      
                                                                                                                                 
        $DIYdistance_LEFT = substr(trim($sonar_movement[0]),1);
        $DIYdistance = trim($sonar_movement[1]);                                                                                  
        $DIYdistance_RIGHT = trim($sonar_movement[2]);  
        $DIYdistance_DOWN = trim($sonar_movement[3]);
        $DIYleftWheel = trim($sonar_movement[4]);                                                                                 
        $DIYrightWheel = trim($sonar_movement[5]);                                                                                
        $DIYtemperature = trim($sonar_movement[6], '#');        
        
        if($DIYdistance==0) 
            $DIYdistance=99;
        if($DIYdistance_LEFT==0) 
            $DIYdistance_LEFT=99;
        if($DIYdistance_RIGHT==0)
            $DIYdistance_RIGHT=99; 
        //echo "right $DIYrightWheel left $DIYleftWheel";   

        	// ---------------------------------------------------------                                                                      
	// --------------   GLOBALS imu    -----------------------                                                                      
	// ---------------------------------------------------------                                                                            
       $ac_X = substr(trim($imu_data[0]),1);                                                                             
        $ac_Y = trim($imu_data[1]);                                                                                  
        $ac_Z = trim($imu_data[2]);   
        $g_X = trim($imu_data[3]);
        $g_Y = trim($imu_data[4]);                                                                                 
        $g_Z = trim($imu_data[5]);                                                                                
        $head = trim($imu_data[6]);         
        $pitch = trim($imu_data[7]);
        $roll = trim($imu_data[8],'#');
        //echo " IMU:$DIYdistance_LEFT $ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll \n";
       // echo "$DIYdistance_LEFT $DIYdistance $DIYdistance_RIGHT $DIYdistance_DOWN  $DIYleftWheel $DIYrightWheel $DIYtemperature \n";
         //echo "refr $DIYdistance_LEFT $DIYdistance, $DIYdistance_RIGHT, $carIsRunning \n" 
} while(floatval($head) <= 0);
}

    


// ************************************************
// ***************** while read data  *************
// ************************************************
  
function non_block_read($fd, &$data) {
    $read = array($fd);
    $write = array();
    $except = array();
    $result = stream_select($read, $write, $except, 0);
    if($result === false) throw new Exception('stream_select failed');
    if($result === 0) return false;
    $data = stream_get_line($fd, 1);
    return true;
}
$flag=1;
while (!feof($sensors)) {
  
   $x = ""; 
    if(non_block_read(STDIN, $x) && $x == 'q')
    {
    //stop();  
       $exec='@q#';
            $serial->deviceOpen();
          //  sleep(0.5); 
		    $serial->sendMessage($exec);
           // sleep(0.1); 
            $serial->deviceClose();
         //    sleep(1);   
         echo "before break";
         //sleep(1);
            break;
            echo "after break";
    }    // Quit on F10

    sleep(0.01); 
	refreshDIYData($sensors,$imu);
   // forwardCorrection(); 
  //DOwn 3 dista 12 
   // $L_O+=$DIYleftWheel;  
  //  $R_O+=$DIYrightWheel; 
    
    //if($LR_O>10)
    //
 //echo " L $DIYleftWheel R $DIYrightWheel D $DIYdistance_DOWN dis $DIYdistance\n";                                                                                                                      
// ---------------------------------------------------------
// --------------  car position      -----------------------
// ---------------------------------------------------------
		 //carscript();  
         sleep(0.01);
         if($flag)
         {$flag=0;rotate(64,'a');} 
// ---------------------------------------------------------
// --------------   write data for cloud -------------------
// ---------------------------------------------------------
		 writeCloud();
}
echo "Main loop broke";  
?>

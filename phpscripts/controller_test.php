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
echo "MPHKA-1";
// ************************************************
// ***************** include **********************
// ************************************************

// vriski tin thessi tou auto
require_once("/root/phpscripts/functions.php");
require_once("/root/phpscripts/position.php");
                                                                                       
echo "MPHKA0";

                                                                     
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
    echo "MPHKA1";
        $exec='@w'.$rodal.':'.$rodar.'#';
		$serial->deviceOpen();
		$serial->sendMessage("@w080:080#");
		$serial->deviceClose(); 
    sleep(5);
    echo "MPHKA2";
    $serial->deviceOpen();
		$serial->sendMessage("@q#");
		$serial->deviceClose();
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

/*
if (!$imu = fopen("/dev/ttyimu", "r")){            
	echo "Cannot link with imu, wrong usb connected";                    
	exit;                                                                   
}  
  */                                                                     

// ************************************************
// ***************** function shutown  *************
// ************************************************

function shutdown()
{
	$exec='echo @q# > /dev/ttymotor';
	exec($exec);
}


                                                                                

        /*
        //imu x y z gyro(n/s) moires
        */
function refreshDIYData($sensors){ //,$imu) {
	global $DIYdistance_LEFT, $DIYdistance,$DIYdistance_DOWN, $DIYdistance_RIGHT, $DIYleftWheel, $DIYrightWheel, $DIYtemperature;
    //global $IMUx,$IMUy,$IMUz,$GYRO,$MOIRES;
	// ---------------------------------------------------------                                                                      
	// --------------  read sonar   -----------------------                                                                           
	// ---------------------------------------------------------                                                                      
        $buffer = trim(stream_get_line($sensors, 4096, "#"));                                                                                                                                                                                                
        if ( trim($buffer)=="" ){                                                                                                 
              return;                                                                                                           
        }                                                                                                                         
                                                                                                                                  
        $sonar_raw = substr($buffer, 1 , strlen($buffer) - 1 );   //echo $sonar_raw;                                              
        $sonar_movement = explode('*', $buffer);             // echo $sonar_movement;   
    // ---------------------------------------------------------                                                                      
	// --------------  read imu   -----------------------                                                                           
	// --------------------------------------------------------- 
    

/*    $buffer2 = trim(stream_get_line($imu, 4096, "#"));                                                                                                                                                                                                
        if ( trim($buffer2)=="" )
        {                                                                                                 
              return;                                                                                                           
        }                                                                                                                         
                                                                                                                                  
        $imu_raw = substr($buffer2, 1 , strlen($buffer2) - 1 );   //echo $sonar_raw;                                              
        $imu_data = explode('*', $buffer2);             // echo $sonar_movement;  
   */                                                                                                                               
                                                                                                                                  
	// ---------------------------------------------------------                                                                      
	// --------------   GLOBALS sonar    -----------------------                                                                      
	// ---------------------------------------------------------                                                                      
                                                                                                                                  
        $DIYdistance_LEFT = trim($sonar_movement[0]);                                                                             
        $DIYdistance = trim($sonar_movement[1]);                                                                                  
        $DIYdistance_RIGHT = trim($sonar_movement[2]);  
        $DIYdistance_DOWN = trim($sonar_movement[3]);
        $DIYleftWheel = trim($sonar_movement[4]);                                                                                 
        $DIYrightWheel = trim($sonar_movement[5]);                                                                                
        $DIYtemperature = trim($sonar_movement[6]);        
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
      //   $IMU_X = trim($imu_data[0]);                                                                             
     //   $IMU_Y = trim($imu_data[1]);                                                                                  
    //    $IMU_Z = trim($imu_data[2]);  
    //  $IMU_GYRO = trim($imu_data[3]);
     //   $IMU_MOIRES = trim($imu_data[4]);     
}


// ************************************************
// ***************** while read data  *************
// ************************************************
  // forward();
  // sleep(5);
  // stop();
  // die;
  
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

//rm  /tmp/lock/LCK..ttymotor
/*while (!feof($sensors)) {
    $x = ""; 
    if(non_block_read(STDIN, $x) && $x == 'q')
    {
    //stop();  
       $exec='@q#';
            $serial->deviceOpen();
          //  sleep(0.5); 
		    $serial->sendMessage($exec);
           // sleep(0.5); 
            $serial->deviceClose();
         //    sleep(1); 
         echo "before break";
            break;
            echo "after break";
    }    // Quit on F10
}*/
?>

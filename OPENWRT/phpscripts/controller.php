#!/usr/bin/php-cli                                                             
<?php
$par  =  "m:";
$par  .=  "x:";
$par  .=  "y:";
$options = getopt($par);
define('MAP_NAME',trim($options["m"]));
define('BOUNDARY_X', intval(trim($options["x"])));
define('BOUNDARY_Y', intval(trim($options["y"])));                                                                                                

include "/root/dimitris/PHP-Serial/src/PhpSerial.php";

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

require_once("/root/dimitris/functions.php");
require_once("/root/dimitris/position.php");

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
    echo "Cannot link with cloud, wrong usb connected";                    
    exit;                                                                   
}                                                                               

// fopen for read sonar data                                                                            
if (!$sensors = popen("cat /dev/ttysonar & cat /dev/ttyimu | tee", "r")){            
    echo "Cannot link with arduinos, wrong usb connected";                    
    exit;                                                                   
}        

function shutdown()
{
    $exec='echo @q# > /dev/ttymotor';
    exec($exec);
}



function refreshDIYData($sensors) {
    global $DIYdistance_LEFT, $DIYdistance,$DIYdistance_DOWN, $DIYdistance_RIGHT, $DIYleftWheel, $DIYrightWheel, $DIYtemperature,$ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll;
    do {
    //IMU:ac_ GIa accelerometer,g_ Gia gyroscope 
    //global $ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll;
    $buffer = trim(fgets($sensors, 4096)); 
       if ( trim($buffer)=="" ){                                                                                                 
              continue;                                                                                                           
        }                                                                                                                         
                                                                                                                                  
        $sonar_raw = substr($buffer, 1 , strlen($buffer) - 1 );   //echo $sonar_raw;                                              
        $sonar_movement = explode('*', $buffer);             // echo $sonar_movement;
        if(count($sonar_movement) <= 7) {
                                                                                                                                 
        $DIYdistance_LEFT = substr(trim($sonar_movement[0]),1);
        $DIYdistance = trim($sonar_movement[1]);                                                                                  
        $DIYdistance_RIGHT = trim($sonar_movement[2]);  
        $DIYdistance_DOWN = trim($sonar_movement[3]);
        $DIYleftWheel = trim($sonar_movement[4]);                                                                                 
        $DIYrightWheel = trim($sonar_movement[5]);                                                                                
        $DIYtemperature = trim($sonar_movement[6], '#');        
        
        if($DIYdistance==4) 
            $DIYdistance=99;
        if($DIYdistance_LEFT==0) 
            $DIYdistance_LEFT=99;
        if($DIYdistance_RIGHT==0)
            $DIYdistance_RIGHT=99;
        if($DIYdistance_DOWN==7)
            $DIYdistance_DOWN=99;
        //echo "right $DIYrightWheel left $DIYleftWheel"; 
        } else {        
            $imu_data = $sonar_movement;
       $ac_X = substr(trim($imu_data[0]),1);                                                                             
        $ac_Y = trim($imu_data[1]);                                                                                  
        $ac_Z = trim($imu_data[2]);   
        $g_X = trim($imu_data[3]);
        $g_Y = trim($imu_data[4]);                                                                                 
        $g_Z = trim($imu_data[5]);                                                                                
        $head = trim($imu_data[6]);         
        $pitch = trim($imu_data[7]);
        $roll = trim($imu_data[8],'#');
        }
} while($DIYdistance_LEFT < 0.5 || floatval($head) <= 0);
}
  
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
    if((non_block_read(STDIN, $x) && $x == 'q') || trim(file_get_contents('/tmp/controller.run')) != '1')
    {
    //stop();  
       $exec='@q#';
            $serial->deviceOpen();
          //  sleep(0.5); 
            $serial->sendMessage($exec);
           // sleep(0.1); 
            $serial->deviceClose();
         //    sleep(1);   
         //sleep(1);
            break;
            
    }    // Quit on F10
    
     //sleep(0.01); 
    refreshDIYData($sensors);

         carscript();  
}
echo "Main loop broke";  
?>

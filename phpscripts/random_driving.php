#!/usr/bin/php-cli                                                             
<?php                                                                                                            

define ('DEGREES_BETWEEN_DOTS', 72);
define('CAR_WHEEL_RADIUS', 3);
//define('BOUNDARY_X', 101); 
//define('BOUNDARY_Y', 101);
define('OBSTACLE', 10);

//include "/root/phpscripts/PHP-Serial/src/PhpSerial.php";

//set tty
exec("/root/admin/resettty.sh");
                                                                                                                 
// ************************************************
// ***************** GLOBALS **********************
// ************************************************

$DIRECTION='';

//GLOBAL motor for write                                                                                
$DIYmotors = "/dev/ttymotor";           

//GLOBAL tachitita gia w kai s 
//tachitita roda aristeri
$DIYrodal='080';
//tachitita roda dexia
$DIYrodar='080';

//GLOBAL tachitita gia a kai d 
$DIYrodarotation="060";

//GLOBAL tachitita gia stop
$DIYrodastop="060";

// ************************************************
// ***************** include **********************
// ************************************************

// vriski tin thessi tou auto
require_once("/root/phpscripts/position.php");
                                                                                       


                                                                     
// ************************************************
// ***************** fopen **********************
// ************************************************


// fopen for write data cloud                                                                            
if (!$DIYpipecloud = fopen("/root/arduinocloud", "r+")){            
	echo "Cannot link with sonar, wrong usb connected";                    
	exit;                                                                   
}                                                                               

                                                                          
                                              

// ************************************************
// ***************** function cloud *********
// ************************************************

//function for write data cloud
function writeCloud(){
       	//DIYmove       w,s,a,r                               
       	//DIYdirection  moires                                           
       	//DIYposx       x thessi               
       	//DIYposy       y thessi                              
       	$DIYmove = trim($GLOBALS['curMove']);         
       	$DIYdirection = trim($GLOBALS['direction']);
       	$DIYposx = trim($GLOBALS['posx']);
       	$DIYposy = trim($GLOBALS['posy']);
       	$DIYcloud="@$DIYmove*$DIYdirection*$DIYposx*$DIYposy#";
       	$DIYcloud .= "\n";    
	$p=$GLOBALS['DIYpipecloud'];
	echo "Coordinates".$DIYcloud;
    fwrite($p, $DIYcloud);
}

// ************************************************
// ***************** while read data  *************
// ************************************************

$GLOBALS['posx'] = 0;
$GLOBALS['posy'] = 0;
while (1) {
// ---------------------------------------------------------
// --------------   write data for cloud -------------------
// ---------------------------------------------------------
$pointX=rand(1,100);
$pointY=rand(1,100);
echo "Next goal [$pointX,$pointY] \n";		
while($GLOBALS['posx']!= $pointX || $GLOBALS['posy']!=$pointY )
{
    if($GLOBALS['posx'] > $pointX) {
        $GLOBALS['posx']--;
    } else if($GLOBALS['posx'] < $pointX) {
          $GLOBALS['posx']++;
    } else if($GLOBALS['posy'] > $pointY) {
         $GLOBALS['posy']--;
    } else if($GLOBALS['posy'] < $pointY) {
         $GLOBALS['posy']++;
    }
    writeCloud();
    usleep(500000);
}

}

?>

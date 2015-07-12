#!/usr/bin/php-cli                                                             
<?php                                                                                                            

include "/root/phpscripts/PHP-Serial/src/PhpSerial.php";

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


	//fopen motor write
	$serial = new phpSerial;
	$serial->deviceSet($DIYmotors);
	$serial->confBaudRate(9600);
	$serial->confParity("none");
	$serial->confCharacterLength(8);
	$serial->confStopBits(1);
	$serial->confFlowControl("none");



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
	fwrite($p, $DIYcloud."\n");
}

// ************************************************
// ***************** function motion move *********
// ************************************************

//function for forward 
function forward()
{
	global $serial, $DIRECTION;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curmove)
	if($curmove != 0){
		echo "allagfhgfhgfhgfhgfhgf $curmove ";
		stopBeforeCD('w', $curmove);
	}

	$DIRECTION='w';

	if($curmove != 'w'){
		registerMove('w');

		$rodal = $GLOBALS['DIYrodal'];
		$rodar = $GLOBALS['DIYrodar'];
		$exec='@w'.$rodal.':'.$rodar.'#';

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	}

}

//function for backward
function backward()
{
	global $serial, $DIRECTION;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curmove)
	stopBeforeCD('s', $curmove);
	$DIRECTION='s';

	if($curmove != 's'){
		echo "backkkkkkkkkkkkk";
		registerMove('s');

		$rodal = $GLOBALS['DIYrodal'];
		$rodar = $GLOBALS['DIYrodar'];
		$exec="@s$rodal:$rodar#";

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	}
}

//function for left - DEPRECATED
/*function left()
{
	global $serial;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curmove)
	stopBeforeCD('a', $curmove);

	if($curmove != 'a'){
		registerMove('a');

		$rodarotation = $GLOBALS['DIYrodarotation'];
		$exec="@a$rodarotation:$rodarotation#";

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	}
}

//function for right - DEPRECATED
function right()
{
	global $serial;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curmove)
	stopBeforeCD('d', $curmove);

	if($curmove != 'd'){
		registerMove('d');

		$rodarotation = $GLOBALS['DIYrodarotation'];
		$exec="@d$rodarotation:$rodarotation#";

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	}
}*/

//function for rotation                                
function rotateLeft($m){                    
        global $serial, $DIYleftWheel, $sensors;      
        $palmos = 32;                       
                                       
        // stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
        $curmove = findregisterMove();                                           
        if( $curmove != 'a' ){                                                   
                stopBeforeCD('a',$curmove);                                          
        }                                                                        
                                                                                 
        registerMove('a');                                                       
                                                                                 
        $rodarotation = $GLOBALS['DIYrodarotation'];                             
        $exec="@a$rodarotation:$rodarotation#";     
                                                    
        $serial->deviceOpen();                      
        $serial->sendMessage($exec);                
        $serial->deviceClose();                     
                                                    
        $curM = 0;                                  
        $prevLeftWheel = 0;                         
        while($curM < $m) {
//var_dump($curM.' '.$DIYleftWheel.' '.$DIYleftWheel);
                if($prevLeftWheel != $DIYleftWheel && $DIYleftWheel == 1) {
                        $curM = $curM + $palmos;                           
                }                                                          
                $prevLeftWheel = $DIYleftWheel;                           
		refreshDIYData($sensors); 
        }                                                                  
                                                                           
        // Send stop                                                       
        stop();                                                            
}   

function rotateRight($m){                                                        
        global $serial, $DIYleftWheel, $sensors;                                           
        $palmos = 32;                                                            
                                                                                 
        // stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
        $curmove = findregisterMove();                                           
        if( $curmove != 'd' ){                                                   
                stopBeforeCD('d',$curmove);                                          
        }                                                                        
                                                                                 
        registerMove('d');                                                       
                                                                                 
        $rodarotation = $GLOBALS['DIYrodarotation'];                             
        $exec="@d$rodarotation:$rodarotation#";                                  
                                                                                 
        $serial->deviceOpen();                                                   
        $serial->sendMessage($exec);                                             
        $serial->deviceClose();                                                  
                                                                                 
        $curM = 0;                                                               
        $prevLeftWheel = 0;                                                     
        while($curM < $m) {                                                      
                if($prevLeftWheel != $DIYleftWheel && $DIYleftWheel == 1) {      
                        $curM = $curM + $palmos;                                 
                }                                                                
                $prevLeftWheel = $DIYleftWheel;                                  
		refreshDIYData($sensors);
        }                                                                        
                                                                                 
        // Send stop                                                             
        stop();                                                                  
}  


//function for stop
function stop()
{
	global $serial, $DIYleftWheel, $sensors;


	//chamilonoume tachitita
	$rodastop = $GLOBALS['DIYrodastop'];

	$curmove = findregisterMove();
	if( $curmove == 'w' ){
		echo "chamilono tachitita w$rodastop:$rodastop ";
		$exec='@w'.$rodastop.':'.$rodastop.'#';
 	}elseif( $curmove == 's'){
		echo "chamilono tachitita s$rodastop:$rodastop ";
		$exec="@s$rodastop:$rodastop#";
	}elseif( $curmove == 'a'){
		echo "chamilono tachitita a$rodastop:$rodastop ";
		$exec="@a$rodastop:$rodastop#";
	}elseif( $curmove == 'd'){
		echo "chamilono tachitita d$rodastop:$rodastop ";
		$exec="@d$rodastop:$rodastop#";
	}

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	
	echo $DIYleftWheel;
	echo " sssssssssssssssssssdsdsdfgdfghgfhgfhjfgf\n";

	if($curmove != 'q' || $curmove != '0'){
		while($DIYleftWheel == 0){sleep(0.5); echo 'sleeping...'."\n"; refreshDIYData($sensors);}
			registerMove('q');
			echo " parking ";
			$exec="@q#";
			$serial->deviceOpen();
			$serial->sendMessage($exec);
			$serial->deviceClose();
	}

}

// ************************************************
// **************** help functions        *********
// ************************************************

// dosse tin direction tou auto empros opisthen aristera dexia
function findregisterMove()
{
	global $curMove;
	if($curMove == 'w'){
		$curmove = 'w';
	}elseif($curMove == 's'){
		$curmove = 's';
	}elseif($curMove == 'a'){
		$curmove = 'a';
	}elseif($curMove == 'd'){
		$curmove = 'd';
	}elseif($curMove == 'q'){
		$curmove = 'q';
	}elseif($curMove == '0'){
		$curmove = '0';
	}

	return $curmove;
}

// stamata ston palmo prin allaxis katefthinsi
//function stopBeforeCD(directionpouthelo, aftopoukaneitora)
function stopBeforeCD($d, $m)
{
	$curmove=$m;
        if( $d == 'w' && $curmove != 'w' ){          
                stop();              
        }elseif( $d == 's' && $curmove != 's'){     
		echo " stopallagis ";
                stop();              
        }elseif( $d == 'a' && $curmove != 'a'){     
                stop();              
        }elseif( $d == 'd' && $curmove != 'd'){     
                stop();              
        }  
}



// ************************************************
// ***************** function shutown  *************
// ************************************************

function shutdown()
{
	$exec='echo @q# > /dev/ttymotor';
	exec($exec);
}


                                                                                


function refreshDIYData($sensors) {
	global $DIYdistance_LEFT, $DIYdistance, $DIYdistance_RIGHT, $DIYleftWheel, $DIYrightWheel, $DIYtemperature;

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
	// --------------   GLOBALS sonar    -----------------------                                                                      
	// ---------------------------------------------------------                                                                      
                                                                                                                                  
        $DIYdistance_LEFT = trim($sonar_movement[0]);                                                                             
        $DIYdistance = trim($sonar_movement[1]);                                                                                  
        $DIYdistance_RIGHT = trim($sonar_movement[2]);  
        $DIYdistance_DOWN = trim($sonar_movement[3]);
        $DIYleftWheel = trim($sonar_movement[4]);                                                                                 
        $DIYrightWheel = trim($sonar_movement[5]);                                                                                
        $DIYtemperature = trim($sonar_movement[6]);     
}




// ************************************************
// ***************** while read data  *************
// ************************************************

while (!feof($sensors)) {
	refreshDIYData($sensors);

        echo "l $DIYleftWheel r $DIYrightWheel";
	echo "\n";                                                                                                                        
// ---------------------------------------------------------
// --------------  car position      -----------------------
// ---------------------------------------------------------
		carscript();

// ---------------------------------------------------------
// --------------   write data for cloud -------------------
// ---------------------------------------------------------
		//writeCloud();
}

?>

#!/usr/bin/php-cli                                                             
<?php                                                                                                            

include "/root/phpscripts/PHP-Serial/src/PhpSerial.php";

//set tty
exec("/root/admin/resettty.sh");
                                                                                                                 
// ************************************************
// ***************** GLOBALS **********************
// ************************************************

$PALMOS=0;
$DIRECTION='';

//GLOBAL motor for write                                                                                
$DIYmotors = "/dev/ttymotor";           

//GLOBAL tachitita gia w kai s 
//tachitita roda aristeri
$DIYrodal='080';
//tachitita roda dexia
$DIYrodar='080';

//GLOBAL tachitita gia a kai d 
$DIYrodarotation="050";

//GLOBAL tachitita gia stop
$DIYrodastop="050";

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
function writeCloud($data){
	$p=$GLOBALS['DIYpipecloud'];
	fwrite($p, $data."\n");
}

// ************************************************
// ***************** function motion move *********
// ************************************************

//function for forward 
function forward()
{
	global $serial, $PALMOS, $DIRECTION;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curmove)
	if($curmove != 0){
		echo "allagfhgfhgfhgfhgfhgf $curmove ";
		stopBeforeCD('w', $curmove);
	}

	$DIRECTION='w';

	if($curmove != 'w' && $PALMOS == 0){
		registerMove('w');

		$rodal = $GLOBALS['DIYrodal'];
		$rodar = $GLOBALS['DIYrodar'];
		//$exec='@w'.$rodal.':'.$rodar.'#';
		$exec='12345678';

echo $exec;
		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
		//$ECHO="echo $exec > /dev/ttymotor";
		//exec($ECHO);
	}

}

//function for backward
function backward()
{
	global $serial, $PALMOS, $DIRECTION;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curmove)
	stopBeforeCD('s', $curmove);
	$DIRECTION='s';

	if($curmove != 's' && $PALMOS == 0){
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

//function for left
function left()
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

//function for right
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
}

//function for rotation
function rotateLeft($m){
	global $serial, $DIYleftWheel;
	$palmos = 32;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	if( $curmove != 'a' ){          
		stopBeforeCD($curmove);
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
		if($prevLeftWheel != $DIYleftWheel && $DIYleftWheel == 1) {
			$curM = $curM + $palmos;
		}
		$prevLeftWheel = $DIYleftWheel;
	}

	// Send stop
	stop();
}

function rotateRight($m){
	global $serial, $DIYleftWheel;
	$palmos = 32;

	// stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo
	$curmove = findregisterMove();
	if( $curmove != 'd' ){          
		stopBeforeCD($curmove);
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
	}

	// Send stop
	stop();
}


//function for stop
function stop()
{
	global $serial, $DIYleftWheel, $PALMOS;


	//chamilonoume tachitita
	$rodastop = $GLOBALS['DIYrodastop'];

	$curmove = findregisterMove();
	if( $curmove == 'w' ){
		echo "chamilono tachitita w$rodastop:$rodastop ";
		$exec='@w'.$rodastop.':'.$rodastop.'w';
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

	if($PALMOS == 0){
		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
			//sleep(1);
	}
	
	echo $DIYleftWheel;
	echo " sssssssssssssssssssdsdsdfgdfghgfhgfhjfgf\n";

/*
			echo " palmos :$PALMOS DIYleftWheel : $DIYleftWheel \n";

			registerMove('q');
			$exec="q";

			$serial->deviceOpen();
			$serial->sendMessage($exec);
			$serial->deviceClose();

			echo " parking ";
			//sleep(1);
*/
	if($curmove != 'q' || $curmove != 0){
	//while($DIYleftWheel == 0){sleep(0.5);}
		if($DIYleftWheel == 0 ){
			echo " perimeno $curmove DIYleftWheel: $DIYleftWheel \n";
			$PALMOS=1;
		}
		echo "PALMOS $PALMOS";

		if($DIYleftWheel == 1 ){
			$PALMOS=0;
			echo " palmos :$PALMOS DIYleftWheel : $DIYleftWheel \n";

			registerMove('q');
			$exec="@q#";

			$serial->deviceOpen();
			$serial->sendMessage($exec);
			$serial->deviceClose();

			echo " parking ";
			//sleep(1);
		}
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
    // This is our shutdown function, in 
    // here we can do any last operations
    // before the script is complete.
    stop();
    exec("echo q > /dev/ttymotor");

    echo "Script executed with success";
}


                                                                                
// ************************************************
// ***************** while read data  *************
// ************************************************

//while ( ($buffer = trim(fgets($sensors, 4096))) !== false    ) { // to alaxa me to parakato gia na diavaso panta ena set apo string   @***#
while (!feof($sensors)) {

// ---------------------------------------------------------
// --------------  read sonar   -----------------------
// ---------------------------------------------------------
    	$buffer = trim(stream_get_line($sensors, 4096, "#"));
                                        
	if ( trim($buffer)=="" ){
              continue;
	}                   

	$sonar_raw = substr($buffer, 1 , strlen($buffer) - 1 );   //echo $sonar_raw;       
	$sonar_movement = explode('*', $buffer);             // echo $sonar_movement;                                                

echo "buffer $buffer";
echo "\n";
echo "substr $sonar_raw";                                                                                                        
echo "\n";

// ---------------------------------------------------------
// --------------   GLOBALS sonar    -----------------------
// ---------------------------------------------------------

	$DIYdistance_LEFT = trim($sonar_movement[0]);                              
	$DIYdistance = trim($sonar_movement[1]);                                   
	$DIYdistance_RIGHT = trim($sonar_movement[2]);                             
	$DIYleftWheel = trim($sonar_movement[3]);
	$DIYrightWheel = trim($sonar_movement[4]);                                                 
	$DIYtemperature = trim($sonar_movement[5]);

// ---------------------------------------------------------
// --------------  car position      -----------------------
// ---------------------------------------------------------
	if($PALMOS == 0){
		carscript();
	}else{
		stopBeforeCD($DIRECTION, $curmove);
		
	}

// ---------------------------------------------------------
// --------------   write data for cloud -------------------
// ---------------------------------------------------------
		//DIYmove     	w,s,a,r
		//DIYdirection	moires
		//DIYposx	x thessi
		//DIYposy	y thessi
		$DIYmove = trim($GLOBALS['curMove']);
		$DIYdirection = trim($GLOBALS['direction']);
		$DIYposx = trim($GLOBALS['posx']);
		$DIYposy = trim($GLOBALS['posy']);
		$DIYcloud="@$DIYmove*$DIYdirection*$DIYposx*$DIYposy#";
		$DIYcloud .= "\n";
		writeCloud($DIYcloud);
}

//register_shutdown_function('shutdown');
?>

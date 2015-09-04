#!/usr/bin/php-cli                                                             
<?php                                                                                                            

include "/root/phpscripts/PHP-Serial/src/PhpSerial.php";

//set tty
//exec("/root/admin/resettty.sh");
                                                                                                                 
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
        $exec='@w'.$rodal.':'.$rodar.'#';
		$serial->deviceOpen();
		$serial->sendMessage("@w080:080#");
		$serial->deviceClose(); 
    sleep(5);
    $serial->deviceOpen();
		$serial->sendMessage("@q#");
		$serial->deviceClose();
        sleep(5);


?>

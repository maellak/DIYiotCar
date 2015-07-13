<?php
// ************************************************
// ***************** function cloud *********
// ************************************************

//function for write data cloud
function writeCloud(){
       	//DIYmove       w,s,a,r                               
       	//DIYdirection  moires                                           
       	//DIYposx       x thessi               
       	//DIYposy       y thessi                               
       	$DIYdirection = trim($GLOBALS['curDirection']);
       	$DIYposx = trim($GLOBALS['posx']);
       	$DIYposy = trim($GLOBALS['posy']);
        /*
        $DIYdistance_LEFT = trim($sonar_movement[0]);                                                                             
        $DIYdistance = trim($sonar_movement[1]);                                                                                  
        $DIYdistance_RIGHT = trim($sonar_movement[2]);  
        $DIYdistance_DOWN = trim($sonar_movement[3]);
        $DIYleftWheel = trim($sonar_movement[4]);                                                                                 
        $DIYrightWheel = trim($sonar_movement[5]);                                                                                
        $DIYtemperature = trim($sonar_movement[6]); 
        */ //x y ari mpo dejia therm imu
       	$DIYcloud="@$DIYdirection*0*$DIYposx*$DIYposy*#";
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
	global $serial, $curDirection;

	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curDirection)
	if($curDirection != 0){
		echo "allagfhgfhgfhgfhgfhgf $curDirection ";
		stopBeforeCD('w', $curDirection);
	}

	if($curDirection != 'w'){ 
		$curDirection = 'w';

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
	global $serial, $curDirection;
	// stopBeforeCD('wkatefthinsi pou theloume na pame', $curDirection)
	stopBeforeCD('s', $curDirection);

	if($curDirection != 's'){
		echo "backkkkkkkkkkkkk";
		$curDirection = 's';

		$rodal = $GLOBALS['DIYrodal'];
		$rodar = $GLOBALS['DIYrodar'];
		$exec="@s$rodal:$rodar#";

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	}
}

//function for rotation                                
function rotateLeft($m){                    
        global $serial, $DIYleftWheel, $sensors, $curDirection;      
        $palmos = 32;                       
                                       
        // stop prin tin allagi katefthinsis to theloume gia na echoume ton palmo                                          
        if( $curDirection != 'a' ){                                                   
                stopBeforeCD('a',$curDirection);                                          
        }                                                                        
                                                                                 
        $curDirection = 'a';                                                       
                                                                                 
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
        global $serial, $DIYleftWheel, $sensors, $curDirection;                                           
        $palmos = 32;                                                            
                                                                                                                          
        if( $curDirection != 'd' ){                                                   
                stopBeforeCD('d',$curDirection);                                          
        }                                                                        
                                                                                 
        $curDirection = 'd';                                                       
                                                                                 
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
	global $serial, $DIYleftWheel, $sensors, $curDirection;

	//chamilonoume tachitita 
	$rodastop = $GLOBALS['DIYrodastop'];

	if( $curDirection == 'w' ){
		echo "chamilono tachitita w$rodastop:$rodastop ";
		$exec='@w'.$rodastop.':'.$rodastop.'#';
 	}elseif( $curDirection == 's'){
		echo "chamilono tachitita s$rodastop:$rodastop ";
		$exec="@s$rodastop:$rodastop#";
	}elseif( $curDirection == 'a'){
		echo "chamilono tachitita a$rodastop:$rodastop ";
		$exec="@a$rodastop:$rodastop#";
	}elseif( $curDirection == 'd'){
		echo "chamilono tachitita d$rodastop:$rodastop ";
		$exec="@d$rodastop:$rodastop#";
	}

		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	
	echo $DIYleftWheel;
	echo " sssssssssssssssssssdsdsdfgdfghgfhgfhjfgf\n";

	if($curDirection != 'q' || $curDirection != '0')
    {
		while($DIYleftWheel == 0)
        {
            sleep(0.5); echo 'sleeping...'."\n"; refreshDIYData($sensors);
        }
			$curDirection = 'q';
			echo " parking ";
			$exec="@q#";
			$serial->deviceOpen();
			$serial->sendMessage($exec);
			$serial->deviceClose();
	}

}

// stamata ston palmo prin allaxis katefthinsi
//function stopBeforeCD(directionpouthelo, aftopoukaneitora)
function stopBeforeCD($d, $m)
{
	$curDirection=$m;
        if( $d == 'w' && $curDirection != 'w' ){          
                stop();              
        }
        elseif( $d == 's' && $curDirection != 's')
        {     
		echo " stopallagis ";
                stop();              
        }
        elseif( $d == 'a' && $curDirection != 'a')
        {     
                stop();              
        }
        elseif( $d == 'd' && $curDirection != 'd')
        {     
                stop();              
        }  
}



?>
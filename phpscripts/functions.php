<?php
// ************************************************
// ***************** function cloud *********
// ************************************************

//function for write data cloud 
$time = time();
/*
function writeCloud(){
    global $time,$DIYdistance_LEFT,$DIYdistance,$DIYdistance_RIGHT,$DIYdistance_DOWN,$DIYleftWheel,$DIYrightWheel,$DIYtemperature,$ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll;
       	//DIYdirection       w,s,a,r                                                                        
       	//DIYposx       x thessi               
       	//DIYposy       y thessi                               
       	$DIYdirection = trim($GLOBALS['curDirection']);
       	$DIYposx = trim($GLOBALS['posx']);
       	$DIYposy = trim($GLOBALS['posy']);     
       	$DIYcloud = "@$time*$DIYdirection*$DIYposx*$DIYposy";//$time 4
        $DIYcloud .= "*$DIYdistance_LEFT*$DIYdistance*$DIYdistance_RIGHT*$DIYdistance_DOWN*$DIYleftWheel*$DIYrightWheel*$DIYtemperature"; //7
        $DIYcloud .= "*$ac_X*$ac_Y*$ac_Z*$g_X*$g_Y*$g_Z*$head*$pitch*$roll";//9
        $DIYcloud .= "*Map_Name1*100*100#" ;//3
        $DIYcloud .= "\n";     
        //echo "CLOUD --  $DIYcloud\n"; 
	$p=$GLOBALS['DIYpipecloud'];
	fwrite($p, $DIYcloud."\n");  
}
*/
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
    echo "MPHKA FORWARD!!!!!!!!!!";
		sleep(0.01);
        $curDirection = 'w';
        $rodal = $GLOBALS['DIYrodal'];
		$rodar = $GLOBALS['DIYrodar'];
        $exec='@w'.$rodal.':'.$rodar.'#';
        /*$LR_O = $L_O-$R_O;     
        if($LR_O>10)
            $rodar+=1;
        else if($LR_O<-10)
            $rodal+=1;
		$exec='@w'.$rodal.':'.$rodar.'#';
        echo "L_O = $L_O and R_O=$R_O diff = $LR_O";*/
        $serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	}
    //else{
        //$curDirection = 'w';
        //$LR_O = $L_O-$R_O;     
		//$rodal = $GLOBALS['DIYrodal'];
		//$rodar = $GLOBALS['DIYrodar'];
        /*if($LR_O>10)
            $rodar+=1;
        else if($LR_O<-10)
            $rodal+=1;
		$exec='@w'.$rodal.':'.$rodar.'#';
        echo "L_O = $L_O and R_O=$R_O diff = $LR_O";
		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
        */
    //}

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

	/*if( $curDirection == 'w' ){
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
	}*/
    if($curDirection != 'q')
    {
        sleep(0.01);
        $curDirection='q';
        $exec = "@q#";
		$serial->deviceOpen();
		$serial->sendMessage($exec);
		$serial->deviceClose();
	    echo "!STOP!";
        //sleep(1);
        //$carIsRunning = TRUE;
        
    }
	//echo $DIYleftWheel;
	//echo " sssssssssssssssssssdsdsdfgdfghgfhgfhjfgf\n";

	/*if($curDirection != 'q' || $curDirection != '0')
    {
		while($DIYleftWheel == 0)
        {
            //sleep(0.5); echo 'sleeping...'."\n"; 
            refreshDIYData($sensors);
        }
			$curDirection = 'q';
			echo " parking ";
			$exec="@q#";
			$serial->deviceOpen();
			$serial->sendMessage($exec);
			$serial->deviceClose();
	}
*/
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
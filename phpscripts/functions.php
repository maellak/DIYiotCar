<?php
// ************************************************
// ***************** function cloud *********
// ************************************************

//function for write data cloud 
$time = time();

function writeCloud(){
    global $time,$DIYdistance_LEFT,$DIYdistance,$DIYdistance_RIGHT,$DIYdistance_DOWN,$DIYleftWheel,$DIYrightWheel,$DIYtemperature,$ac_X,$ac_Y,$ac_Z,$g_X,$g_Y,$g_Z,$head,$pitch,$roll;
        //DIYdirection       w,s,a,d                                                                        
        //DIYposx       x thessi               
        //DIYposy       y thessi                               
        // fopen for write data cloud                                                                            
if (!$DIYpipecloud = fopen("/root/arduinocloud", "r+")){            
    echo "Cannot link with cloud, wrong usb connected";                    
    exit;                                                                   
}     
        
        $DIYdirection = trim($GLOBALS['curDirection']);
        $DIYposx = trim($GLOBALS['posx']);
        $DIYposy = trim($GLOBALS['posy']);     
        $DIYcloud = "@$time*$DIYdirection*$DIYposx*$DIYposy";//$time 4
        $DIYcloud .= "*$DIYdistance_LEFT*$DIYdistance*$DIYdistance_RIGHT*$DIYdistance_DOWN*$DIYleftWheel*$DIYrightWheel*$DIYtemperature"; //7
        $DIYcloud .= "*$ac_X*$ac_Y*$ac_Z*$g_X*$g_Y*$g_Z*$head*$pitch*$roll";//9
        $DIYcloud .= "*".MAP_NAME."*".BOUNDARY_X."*".BOUNDARY_Y."#";//3
        $DIYcloud .= "\n";     
        echo "CLOUD --  $DIYcloud\n"; 
    //$p=$GLOBALS['DIYpipecloud'];
    $p = $DIYpipecloud;
    fwrite($p, $DIYcloud."\n");  
    
}
 
// ************************************************
// ***************** function motion move *********
// ************************************************

//function for forward 
function forward()
{
    global $serial, $curDirection;
    
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
    //// stopBeforeCD('wkatefthinsi pou theloume na pame', $curDirection)
    //stopBeforeCD('s', $curDirection);

    /* 
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
    */
        if($curDirection != 's')
    {
        $curDirection = 's' ;
        sleep(0.01);
        $rodal = $GLOBALS['DIYrodal'];
        $rodar = $GLOBALS['DIYrodar'];
        $exec = "@s".$rodal.":".$rodar."#";
        $serial->deviceOpen(); 
        $serial->sendMessage($exec);
        $serial->deviceClose();
        echo "!BACK!";
        //sleep(1);
        //$carIsRunning = TRUE;       
    }
    
    
}

//function for rotation                                

function rotate($m,$d){                    
        global $serial, $DIYleftWheel, $sensors,$imu,$curDirection,$head;
 
        if($curDirection!='a' || $curDirection!='d')
        {
            echo "Rotate\n";
            stop();
            sleep(0.01);
            if($curDirection=='a')
              $wantedMoires = $head-$m;  
            else 
             $wantedMoires = $head+$m; 
         
            $curDirection = $d;           
            if($wantedMoires <0)
            $wantedMoires = 360 + $wantedMoires;
            //sleep(0.01);
            $exec = "@a060:063#";
            $serial->deviceOpen();                      
            $serial->sendMessage($exec);                
            $serial->deviceClose();  
            while(!($wantedMoires-2<= $head && $head <=$wantedMoires+2))
            {
                refreshDIYData($sensors,$imu);
                echo " wanted moires $wantedMoires  head $head  \n" ;               
            } 
            stop();
            sleep(0.01);            
        }
      
       
}  


//function for stop
function stop()
{
    global $serial, $DIYleftWheel, $sensors, $curDirection;

    //chamilonoume tachitita 
    //$rodastop = $GLOBALS['DIYrodastop'];

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
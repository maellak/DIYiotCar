<?php
$curMove = '0';
$direction = 90;
$posx = 0;
$nextPosx = 0;
$posy = 0;
$nextPosy = 0;
define ('DEGREES_BETWEEN_DOTS', 36); 
define('CAR_WHEEL_RADIUS', 3);
define('OBSTACLE', 10);
define('GROUND_DISTANCE', 4);
$carIsRunning=TRUE;

function recalculateCoordinates() {
    global $curDirection, $head;
    global $posx, $posy, $nextPosx, $nextPosy;
    if($curDirection == 'w') {
        $nextPosx = $posx + CAR_WHEEL_RADIUS*deg2rad(DEGREES_BETWEEN_DOTS)*sin(deg2rad($head));
        $nextPosy = $posy + CAR_WHEEL_RADIUS*deg2rad(DEGREES_BETWEEN_DOTS)*cos(deg2rad($head));
    
    } elseif($curDirection == 's') {
        $nextPosx = $posx - CAR_WHEEL_RADIUS*deg2rad(DEGREES_BETWEEN_DOTS)*sin(deg2rad($head));
        $nextPosy = $posy - CAR_WHEEL_RADIUS*deg2rad(DEGREES_BETWEEN_DOTS)*cos(deg2rad($head));
        $direction = $direction + 160;
    }
}

$prevDIYLeftWheel = 0;


// Default movement and obstacle detection logic
function movementLogic() {

    global $posx, $posy,$serial,$curDirection,
 $curMove, $direction, $DIYdistance_LEFT, $DIYdistance, $DIYdistance_RIGHT, $carIsRunning;

     echo "left " . $DIYdistance_LEFT . "\n";
    echo "center" . $DIYdistance . "\n";
    echo "right" . $DIYdistance_RIGHT . "\n";
    echo "Current" . $curDirection . "\n";
        if ( $carIsRunning ) { 
            //echo " forward ";
            if (  (  $DIYdistance_RIGHT >= OBSTACLE && $DIYdistance_LEFT >= OBSTACLE && $DIYdistance >= OBSTACLE ) && ( ($curDirection != 's') && ($curDirection != 'a') && ($curDirection != 'd') ) ){    
                forward();//
                $carIsRunning = TRUE;
            } 
            else if(  $DIYdistance_RIGHT < OBSTACLE && $DIYdistance_LEFT < OBSTACLE && $DIYdistance <= OBSTACLE )
            {
                backward();
                $carIsRunning = TRUE;
            }          
           /*elseif (($curMove != 'a' && $curMove != 'd') && ($DIYdistance_LEFT > $DIYdistance_RIGHT) && ($DIYdistance_LEFT > OBSTACLE)){ 
                rotate(64,'a');//left(); 
                //stop();
                $carIsRunning = TRUE;
 
            }                       
            elseif (($curMove != 'd' && $curMove != 'a') && ($DIYdistance_LEFT < $DIYdistance_RIGHT) && ($DIYdistance_RIGHT > OBSTACLE))
            { 
                rotate(64,'d');//right();
                //stop();
                $carIsRunning = TRUE;
            }*/
        /*   
        elseif ( ($DIYdistance_RIGHT <= OBSTACLE) && ($DIYdistance_LEFT <= OBSTACLE) && ($DIYdistance <= OBSTACLE) ) {              
                stop();
                echo "stop\n";
                $carIsRunning = false;
            }  
            */ 
            else{  
                 stop();
                $carIsRunning = TRUE;//FALSE;
            } 

            
        }       
}

function carscript() {

    global $posx, $posy, $nextPosx, $nextPosy, $DIYleftWheel, $DIYrightWheel, $prevDIYLeftWheel, $direction,$curDirection;
    // Car logic for boundaries
    // Recalculate position
    if($DIYleftWheel == 1 && $prevDIYLeftWheel != $DIYleftWheel) {
        recalculateCoordinates();
    }
    $prevDIYLeftWheel = $DIYleftWheel;
        // we are still inside the boundary. Update posx and posy and execute normal logic.
        $posx = $nextPosx;
        $posy = $nextPosy;
        movementLogic();        
}

register_shutdown_function(function() {
    shutdown();
});

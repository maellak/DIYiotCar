<?php

$fp = fopen("/dev/ttyACM1", "w");


function runCar()
{
    $carIsRunning=true;
    echo 'Running!';
    //Serial.println("R");
    fwrite($fp, chr(w));
}

function stopCar( $dist )
{
    findObject( dist );
    $carIsRunning=false;
    echo 'Car stop';
     fwrite($fp, chr( ));

    //Serial.println("S");
}

function noObject()
{
    //Serial.println("");
}

function findObject( $dist )
{
  //dist=int( dist*10 )/10;
  //for($i=0;$i<=$dist;$i++)
  //{
    //Serial.print(" ");
 // }
  //Serial.println("|");
}

function turnLEFT()
{
   // Serial.println("L");
   echo 'turn left';
    fwrite($fp, chr(a));

}

function turnRIGHT()
{
   // Serial.println("R");
   echo 'turn right';
  fwrite($fp, chr(d));

}

fclose($fp);

?>

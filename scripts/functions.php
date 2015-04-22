<?php


		function runCar($signal){
		
		    $carIsRunning=true;
		    echo 'Running!';
        $move = 'w';
        echo $move;
		    fwrite($signal, $move);
		
		}
		
		function stopCar($dist, $signal)
		{
		    findObject( dist );
		    $carIsRunning=false;
		    echo 'Car stop!';
        $move = 's'; 
		    fwrite($signal, $move);
		
		}
		
		
		function turnLEFT(){
		   
		  echo 'turn left';
      $move = 'a';
		  fwrite($signal, $move;
		
		}
		
		function turnRIGHT(){
		
		  echo 'turn right';
      $move = 'd';
		  fwrite($signal, $move);
		
		}
		
		function findObject( $dist ){
		
		  //$dist=int( dist*10 )/10;
      
		  for($i=0;$i<=$dist;$i++)
		  {
		    echo "*";
		  }
		  echo "|\n";
		}
		
		
		function noObject(){
		
		    echo "No object, keep moving :)";
		}


?>

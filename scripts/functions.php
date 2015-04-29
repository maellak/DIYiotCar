<?php

		
		
function runCar($signal,$move){
		
	echo 'Running!';
	fwrite($signal, $move);
		
}
		
function stopCar($dist, $signal,$move){
	
	findObject($dist);
	echo 'Car stop!'; 
	fwrite($signal, $move);
		
}
		
		
function turnLEFT($signal,$move){
		   
	echo 'turn left';
	fwrite($signal, $move);
		
}
		
function turnRIGHT($signal,$move){
		
	echo 'turn right';
	fwrite($signal, $move);
		
}
		
function findObject( $dist ){
		
	//$dist=int( dist*10 )/10;
      
	for($i=0;$i<=$dist;$i++){
	
		 echo "*";
	}
	echo "|\n";
}
		
		
function noObject(){
		
	echo "No object, keep moving :)";
}


?>

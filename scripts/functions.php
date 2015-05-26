<?php

		
		
function runCar($signal){
		
	
	fwrite($signal, $move);
	echo "Running!\n";	
}
		
function stopCar($dist, $signal,$move){
	
	findObject($dist);
	fwrite($signal, $move);
	echo "Car stop!\n"; 
}
		
		
function turnLEFT($signal){
		   
	fwrite($signal, $move);
	echo "turn left\n";	
}
		
function turnRIGHT($signal){
		
	fwrite($signal, $move);
	echo "turn right\n";
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

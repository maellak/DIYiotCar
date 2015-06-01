<?php

		
		
function runCar($signal,$move){
		
	
	myfwrite($signal, $move);
	echo "Running!\n";	
}
		
function stopCar( $signal,$move){
	
	
	myfwrite($signal, 'q');
	sleep(0.1);
	myfwrite($signal, $move);
	echo "Car stop!\n"; 
}

function myfwrite(&$signal,$move){

	if ($signal == NULL || is_string($signal) ) {

		$signal = fopen("/dev/ttymotor", "w");
		echo shell_exec("sh /root/admin/resetusbarduino.sh");

		# code...
	}

	return fwrite($signal,$move);


}
	
function brakeCar($signal,$move){

	
	myfwrite($signal, $move);
	echo "Brake stop!\n"; 

}	
		
function turnLEFT($signal){
		   
	myfwrite($signal, $move);
	echo "turn left\n";	
}
		
function turnRIGHT($signal){
		
	myfwrite($signal, $move);
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
		
	echo "No object, keep moving :)\n";
}


?>

<?php                                                                                                            
                                                                                                                 
include 'functions.php';                                                                                         


$carIsRunning=TRUE;                                                         
$carBackwards=FALSE;                                                        
$frenoDistance=15;   
$command = ""; // set current direction to forward
$temp_command = "";     // temporary direction command to be written                                                    
                                                                          
                                                                            
if (!$sensors = fopen("/dev/ttyS11", "r")){            // usb1, sonar sensors input , read on file descriptor
                                                                            
    echo "Cannot link with sonar, wrong usb connected";                    
    exit;                                                                   
                                                                                
}                                                                               
                                                                                
                                              
                                                                                
                                                                                
                                                                                
                                                                                             
        while ( ( ($buffer = fgets($sensors, 131072)) !== false) || fread(STDIN,1) == ' ') {    

echo $buffer;                                                                         
                                                                                                                 
                                                                                                                 

} // motors 



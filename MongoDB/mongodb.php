<?php

    require __DIR__ . '/vendor/autoload.php';
// **********************************************//
// afto edo einai o pipe server 
// prepei na to trexete gia na ipodechtei ta data apo tonsensora pou stelnte me to mongopusher
// to trechoume apla me   php mongodp.php
//**************************************************************// 
    echo "1";
    $loop   = React\EventLoop\Factory::create();
echo "2";
    $context = new React\ZMQ\Context($loop);
echo "3";
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
echo "4";
    $pull->bind('tcp://127.0.0.1:5556'); // Binding to 127.0.0.1 means the only client that can connect is itself
echo "5";
    $pull->on('message', function ($m) {
echo "6";
	// edo messa tora vasete ton kodika pou thelete
	// mporeite na ta valete polla masi 
	// kai meta na ta sosete stin vassi
	// prosexte omos na min para pola logo ram
	var_dump($m);

// apo to $pid = pcntl_fork(); mechri to klissimo tou switch echoume ton tropo pou kanoume fork ena process 
// afto to theloume gia na min echoume kathiosterissi
	$pid = pcntl_fork();

	    switch($pid) {
		case -1:
		    print "Could not fork!\n";
		    exit;
		case 0:
		// edo echete kanei fork opote mporeite na trexete oti thelete
		    exec("./archeiogiainsertstinvassi -d'$m'  2>&1", $output, $return_var);	
		    break;
//		default:
//		   echo "start";
	       }

    });


    $loop->run();
?>
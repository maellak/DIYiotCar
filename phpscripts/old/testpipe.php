<?php
 $pipeB = fopen("/root/x",'r');
    $valueLen = fread($pipeB, 1);
    echo $valueLen;
?>

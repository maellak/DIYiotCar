<?php
function forward()
{
return shell_exec('echo "w" > /dev/ttymotor');
}

function backward()
{
return shell_exec('echo "s" > /dev/ttymotor');
}
function left()
{
return shell_exec('echo "a" > /dev/ttymotor');
}
function right()
{
return shell_exec('echo "w" > /dev/ttymotor');
}
function stop()
{
return shell_exec('echo "q" > /dev/ttymotor');
}
?>

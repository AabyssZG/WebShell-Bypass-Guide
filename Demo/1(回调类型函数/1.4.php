<?php
error_reporting(0);
function test_odd($var)
{
    return($var & 1);
}
 
$a1=array("a","b",2,3,4);
print_r(array_filter($a1,"test_odd"));

?>

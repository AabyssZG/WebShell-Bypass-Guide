<?php
function myfunction($v)
{
return($v*$v);
}
 
$a=array(1,2,3,4,5);    //array(1,4,9,16,25)
print_r(array_map("myfunction",$a));

?>

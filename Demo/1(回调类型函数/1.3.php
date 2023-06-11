<?php
function myfunction($value,$key,$p)
{
echo "The key $key $p $value<br>";
}
$a=array("a"=>"red","b"=>"green","c"=>"blue");
array_walk($a,"myfunction","has the value");

?>

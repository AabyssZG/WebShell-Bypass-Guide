<?php
error_reporting(0);
parse_str("name=Peter&age=43");
echo $name."<br>";      //Peter
echo $age;              //43

parse_str("name=Peter&age=43",$myArray);
print_r($myArray);       //Array ( [name] => Peter [age] => 43 )

?>

<?php
$file = fopen("test.txt","w");
echo fwrite($file,"Hello World. Testing!");    //21
fclose($file);

?>

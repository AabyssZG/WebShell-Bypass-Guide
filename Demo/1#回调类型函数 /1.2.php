<?php
function test() 
{ 
 echo '这个是中止方法test的输出'; 
} 
  
register_shutdown_function('test'); 
  
echo 'before' . PHP_EOL; 
exit(); 
echo 'after' . PHP_EOL;

?>

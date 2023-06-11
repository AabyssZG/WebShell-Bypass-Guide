<?php
exec( 'ls' , $result );    //执行Linux的读取目录命令
//exec( 'dir' , $result );  //执行Windows的读取目录命令
print_r($result);        //Array ( [0] => index.php )

?>

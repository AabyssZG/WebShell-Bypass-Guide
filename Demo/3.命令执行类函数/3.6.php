<?php
$result = popen('ls', 'r');    //参数1:执行Linux的ls命令 参数2:字符串类型
//$result = popen('dir', 'r');    //参数1:执行Windows的dir命令 参数2:字符串类型
echo fread($result, 100);      //参数1:上面生成的资源 参数2:读取100个字节

?>

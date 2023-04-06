<?php
$r = 'b[]=AabyssZG&b[]=system';
$rce = array();      //用array函数新建数组
parse_str($r, $rce); //这个函数下文有讲
print_r($rce);

?>

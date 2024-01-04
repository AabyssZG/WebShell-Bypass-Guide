<?php
define('-_-','smile');    //特殊符号开头，定义特殊常量
echo constant("-_-");     //不能直接echo特殊常量，否则会报错
define('wo',3.14);
const wo = 3;

echo wo;

?>

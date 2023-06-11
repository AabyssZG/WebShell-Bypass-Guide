<?php
function confusion($a){
    $s = ['A','a','b', 'y', 's', 's', 'T', 'e', 'a', 'm'];
    $tmp = "";
    while ($a>10) {
        $tmp .= $s[$a%10];
        $a = $a/10;
    }
    return $tmp.$s[$a];
}
echo confusion(976534);         //sysTem（高危函数）

?>

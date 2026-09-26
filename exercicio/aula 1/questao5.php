<?php

$ano_e_bissexto = 2025;

if($ano_e_bissexto % 4== 0 && ($ano_e_bissexto % 100 != 0 || $ano_e_bissexto % 400 == 0)){
    echo"Verdade";
}else{
    echo "Falso";
}
?>

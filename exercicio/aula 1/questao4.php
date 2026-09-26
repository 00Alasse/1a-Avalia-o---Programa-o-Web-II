<?php

$media = 4;

if ( $media >= 7){
    echo "Aprovado";
}elseif ($media >= 5 && $media <= 6.9){
    echo "Recuperação";
}else{
    echo "Reprovado";
}

?>
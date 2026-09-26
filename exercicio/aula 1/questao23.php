<?php

function par_impar($n){
    if($n % 2 == 0){
        return "Par";
    }else{
    return "Impar";
    }
}

echo par_impar(5);
echo "<br>";
echo par_impar(10);
?>
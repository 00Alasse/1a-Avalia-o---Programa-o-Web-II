<?php
$numero = 8;

if(($numero > 0)&&($numero %2== 0)) {
    echo"Positivo";
} else if(($numero %2== 1)&&($numero >0)) {
    echo "Negativo";
} else {
    echo "Zero";
}
?>
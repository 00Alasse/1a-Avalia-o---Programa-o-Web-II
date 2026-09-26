<?php
$lado1 = 4;
$lado2 = 5;
$lado3 = 6;

if (($lado1 + $lado2) > $lado3 && ($lado1 + $lado3)>$lado2 && ($lado2 + $lado3)>$lado1) {
    echo"É um triâgulo";
}else{
    echo "Não é um triâgulo";
}
?>
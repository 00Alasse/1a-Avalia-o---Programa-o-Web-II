<?php
$compra = 199;

if($compra > 500){
    echo"Desconto de 15%";
}elseif(($compra >= 200) && ($compra <= 500)){
    echo "Desconto de 10%";
}else{
    echo "Sem desconto";
}
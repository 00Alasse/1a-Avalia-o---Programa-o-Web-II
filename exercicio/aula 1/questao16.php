<?php
    $n = 7;
    $el_ant = 0;
    $el_atual = 1;

    for ($i = 1; $i < $n; $i++) {
        $aux = $el_ant+$el_atual;
        $el_ant = $el_atual;
        $el_atual = $aux;
    }

    echo "{$el_atual}";

?>


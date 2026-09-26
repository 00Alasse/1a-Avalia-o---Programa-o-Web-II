<?php
    function fatorial($n){
        $fat = 1;
        for($i=1;$i<$n;$i++){
            $fat = $fat * $i;
        }
        return $fat;

    }

    echo fatorial(1);
?>
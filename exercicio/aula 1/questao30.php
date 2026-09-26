<?php
   function soma($array) {
        $r = 0;
        for( $i = 0; $i < count($array); $i++ ) {
        $r = $r + $array[ $i ];
        }
        return $r;
   }

    $array = [3,6,8,2];
    echo soma($array);
?>
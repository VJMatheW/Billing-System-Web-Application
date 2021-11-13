<?php

echo "hello ";
$s = "123as";
$s =  preg_replace( '/[^0-9]/', '', $s);
echo $s;
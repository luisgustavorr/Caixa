<?php 
$barcode = "2000050002403";
$barcode = str_split($barcode);
if($barcode[0] == 2){
$codigo_produto = $barcode[1].$barcode[2].$barcode[3].$barcode[4].$barcode[5];
$valorproduto = $barcode[6].$barcode[7].$barcode[8].$barcode[9].$barcode[10].$barcode[11];
$valorproduto = $valorproduto/100;
}

print_r($codigo_produto);
?>
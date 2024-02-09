<?php 
include('../../MySql.php');
$produto = $_POST["produto"];
$caixa = $_COOKIE["caixa"];
$select_produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE id = ?");
$select_produto->execute(array($produto));
$select_produto = $select_produto->fetch();
$array_precos = json_decode($select_produto["json_precos"],true);
$array_precos[$caixa] = $_POST["preco"];
$obg_json = json_encode($array_precos);
echo $_POST["preco"];
print_r(json_encode($array_precos));
$select_produto = \MySql::conectar()->prepare("UPDATE `tb_produtos` SET `json_precos` = ?  WHERE `tb_produtos`.`id` = ?");
$select_produto->execute(array($obg_json ,$produto));

?>
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

$produto = \MySql::conectar()->prepare("UPDATE `tb_produtos` SET `nome` = ?, `codigo` = ?, `por_peso` = ?, `codigo_id` = ?, `vendido` = ?, `ncm` = ?, `cod_grp_financeiro` = ?, `cst_icms` = ?, `icms` = ?, `cst_pis_cofins` = ?,`validade` = ? WHERE `tb_produtos`.`id` = ?");
$produto->execute(array($_POST['nome'],$_POST['codigo'],$_POST['por_peso'],$_POST['codigo_id'],1,$_POST["ncm"],0,$_POST["cst_icms"],$_POST["icms"],$_POST["cst_pis_cofins"],$_POST["validade"], $_POST["produto"]));

$select_produto = \MySql::conectar()->prepare("UPDATE `tb_produtos` SET `json_precos` = ?  WHERE `tb_produtos`.`id` = ?");
$select_produto->execute(array($obg_json , $_POST["produto"]));

?>
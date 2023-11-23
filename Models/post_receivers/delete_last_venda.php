<?php 
include('../../MySql.php');
$select_last_venda = \MySql::conectar()->prepare("SELECT * FROM `tb_vendas` WHERE `pedido_id` = 0 AND `caixa` = ?
ORDER BY `tb_vendas`.`id` DESC
LIMIT 1;");
$select_last_venda->execute(array($_COOKIE["caixa"]));
$select_last_venda = $select_last_venda->fetch();

if($select_last_venda["forma_pagamento"] == "Dinheiro"){
    $update_valor_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_no_caixa` = `valor_no_caixa` - ?, `valor_atual` = `valor_atual`-? WHERE `tb_caixas`.`caixa` = ? ");
    $update_valor_caixa->execute(array($select_last_venda["valor"],$select_last_venda["valor"],$_COOKIE["caixa"]));
}else{
    $update_valor_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET  `valor_atual` = `valor_atual`-?WHERE `tb_caixas`.`caixa` = ? ");
    $update_valor_caixa->execute(array($select_last_venda["valor"],$_COOKIE["caixa"]));
}
    $equip = \MySql::conectar()->prepare("DELETE FROM `tb_vendas` WHERE `pedido_id` = 0 AND `caixa` = ?
    ORDER BY `tb_vendas`.`id` DESC
    LIMIT 1;");
    $equip->execute(array($_COOKIE["caixa"]));
?>
<?php 
include('../../MySql.php');
$produto = \MySql::conectar()->prepare("SELECT `valor_no_caixa` AS 'Valor_total' FROM `tb_caixas` WHERE caixa = '".trim($_POST['caixa'])."'");
$produto->execute();
$produto = $produto->fetch();
echo $produto['Valor_total'];
// $valorDinheiro = \MySql::conectar()->prepare("SELECT SUM(valor) as valor FROM `tb_vendas` LEFT JOIN `tb_pedidos` ON `tb_vendas`.`pedido_id` = `tb_pedidos`.`id` WHERE DATE(`tb_vendas`.`data`) = '2023-12-26' AND `tb_vendas`.`forma_pagamento` ='Dinheiro' AND `tb_vendas`.`colaborador` = '9841' AND ((`tb_vendas`.`produto` LIKE 'Entrada%' AND  `tb_pedidos`.`entregue` = 0) OR (`tb_vendas`.`produto` NOT LIKE 'Entrada%' AND  `tb_pedidos`.`entregue` = 1) OR `tb_vendas`.`pedido_id` = 0)");
// $valorDinheiro->execute();
// $valorDinheiro = $valorDinheiro->fetch();
// $valorSangria = \MySql::conectar()->prepare("SELECT SUM(`valor`) as valor
// FROM `tb_sangrias`
// WHERE DATE(`data`) = '2023-12-26' AND `colaborador` = '9841' GROUP BY DATE(`data`) 
// ");
// $valorSangria->execute();
// $valorSangria = $valorSangria->fetch();

// if( isset($valorDinheiro['valor']) AND isset($valorSangria['valor'])){
//     $valorTotal = $valorDinheiro['valor'] - $valorSangria['valor'] ;
//     echo $valorTotal;

// }elseif(isset($valorDinheiro['valor'])) {
//     $valorTotal = $valorDinheiro['valor'] ;
//     echo $valorTotal;
// }


?>

<?php 
include('../../MySql.php');
  $id_teste = 301;
  $updt_Pedido = \MySql::conectar()->prepare("UPDATE `tb_pedidos` SET `entregue` = '1' WHERE `tb_pedidos`.`id` = ? ");
  $updt_Pedido->execute(array($_POST['pedido']));
  $slct_Pedido = \MySql::conectar()->prepare("SELECT * FROM `tb_pedidos` WHERE `tb_pedidos`.`id` = ? ");
  $slct_Pedido->execute(array($_POST['pedido']));
  $slct_Pedido = $slct_Pedido->fetch();
  $data_atual =date("Y-m-d h:i:sa");
  $produtosArray = json_decode($slct_Pedido['produtos'], true);
  function conectarAoBanco()
{
    return \MySql::conectar();
}

  $db = conectarAoBanco();
  foreach ($produtosArray as $key => $value) {
    $produtoStmt = $db->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`,`pedido_id`,`quantidade_produto`) VALUES (NULL, ?,?, ?, ?, ?,?,?,?); ");
    $produtoStmt->execute(array($slct_Pedido['colaborador'], $data_atual, $value['preco'], $slct_Pedido['caixa'], $value['id'], $slct_Pedido['forma_pagamento'], $slct_Pedido['id'], $value['quantidade']));

    if($slct_Pedido["forma_pagamento"] == 'Dinheiro'){
        $atualizarCaixaStmt = $db->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ?,`valor_no_caixa` = `valor_no_caixa` + ?   WHERE `tb_caixas`.`caixa` = ? ");
        $atualizarCaixaStmt->execute(array($value['preco'], $value['preco'], $slct_Pedido['caixa']));
    }else{

        $atualizarCaixaStmt = $db->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ? ");
        $atualizarCaixaStmt->execute(array($value['preco'], $slct_Pedido['caixa']));
    }
    $produtoNomeStmt = $db->prepare("SELECT nome FROM `tb_produtos` WHERE  `id` =?");
    $produtoNomeStmt->execute(array($value['id']));
    $produtoNome = $produtoNomeStmt->fetch();
print_r($produtosArray);

}
print_r($slct_Pedido);
    echo $id_teste
?>
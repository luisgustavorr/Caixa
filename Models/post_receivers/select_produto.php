<?php 
include('../../MySql.php');
  if(isset($_POST['editando_pedido'])){
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `id` = ?");
    $produto->execute(array($_POST['produto']));
    $produto = $produto->fetch();
    echo json_encode($produto,JSON_UNESCAPED_UNICODE);
  }else{
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ? AND `preco` > 0");
  $produto->execute(array($_POST['barcode']));
  $produto = $produto->fetch();
  echo json_encode($produto,JSON_UNESCAPED_UNICODE);
  }
?>
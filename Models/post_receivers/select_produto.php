<?php 
include('../../MySql.php');
  if(isset($_POST['editando_pedido'])){
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
    $produto->execute(array($_POST['produto']));
    $produto = $produto->fetch();
    if(empty($produto)){
      print_r($_POST['produto']);
    }else{
      echo json_encode($produto,JSON_UNESCAPED_UNICODE);
    }

  }else{
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
  $produto->execute(array($_POST['barcode']));
  $produto = $produto->fetch();
  echo json_encode($produto,JSON_UNESCAPED_UNICODE);
  }
?>
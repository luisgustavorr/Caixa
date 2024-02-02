<?php
include('../../MySql.php');
if (isset($_POST['editando_pedido'])) {
  $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
  $produto->execute(array($_POST['produto']));
  $produto = $produto->fetch();
  if (empty($produto)) {
    print_r($_POST['produto']);
  } else {
    echo json_encode($produto, JSON_UNESCAPED_UNICODE);
  }
} else {
  $barcode = $_POST['barcode'];
  $barcode = str_split($barcode);
  if ($barcode[0] == 2 and isset($barcode[12])) {
    $codigo_produto = $barcode[1] . $barcode[2] . $barcode[3] . $barcode[4] . $barcode[5];
    $valorproduto = $barcode[6] . $barcode[7] . $barcode[8] . $barcode[9] . $barcode[10] . $barcode[11];
    $valorproduto = $valorproduto / 100;
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ? AND por_peso = 1");
    $produto->execute(array($codigo_produto));
    $produto = $produto->fetch();
    $preco_kg = str_replace(",", ".", $produto["preco"]);
    $preco_kg = number_format($preco_kg, 2, ".", "");
    $quantidade_produto = $valorproduto / $preco_kg;
    $quantidade_produto = number_format($quantidade_produto, 3, ".", "");
    $produto["quantidade"] = round($quantidade_produto,3);
    $produto["preco_final"] = $valorproduto;

    echo json_encode($produto, JSON_UNESCAPED_UNICODE);
  } else {
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
    $produto->execute(array($_POST['barcode']));
    $produto = $produto->fetch();
    $produto["quantidade"] = 1;
    $produto["preco_final"] = 0;

    echo json_encode($produto, JSON_UNESCAPED_UNICODE);
  }
}

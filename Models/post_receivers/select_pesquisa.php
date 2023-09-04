<?php 
include('../../MySql.php');
if(isset($_POST['codigo'])){
  $produto = \MySql::conectar()->prepare("SELECT *
  FROM `tb_produtos`
  WHERE `codigo` LIKE ? AND preco > 0
  ORDER BY
    CASE
      WHEN `codigo` LIKE ? THEN 1 
      WHEN `codigo` LIKE ? THEN 2 
      ELSE 3 
    END,
    `codigo`;");
  $produto->execute(array('%'.$_POST['pesquisa'].'%',$_POST['pesquisa'].'%','% '.$_POST['pesquisa'].'%'));
  $produto = $produto->fetchAll();
}else{
  $produto = \MySql::conectar()->prepare("SELECT *
  FROM `tb_produtos`
  WHERE `nome` LIKE ? AND preco > 0
  ORDER BY
    CASE
      WHEN `nome` LIKE ? THEN 1 
      WHEN `nome` LIKE ? THEN 2 
      ELSE 3 -- Todas as outras palavras
    END,
    `nome`;");
  $produto->execute(array('%'.$_POST['pesquisa'].'%',$_POST['pesquisa'].'%','% '.$_POST['pesquisa'].'%'));
  $produto = $produto->fetchAll();
}

  echo json_encode($produto);
?>
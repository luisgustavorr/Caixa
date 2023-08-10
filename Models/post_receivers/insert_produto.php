
<?php 
include('../../MySql.php');
    $codigo = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
    $codigo->execute(array($_POST['codigo']));
    $codigo = $codigo->fetch();
    $codigo_id = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo_id` = ?");
    $codigo_id->execute(array($_POST['codigo_id']));
    $codigo_id = $codigo_id->fetch();
    if(!empty($codigo)){
        echo "Codigo_barras_repetido";
    }elseif (!empty($codigo_id)) {
        echo "Codigo_repetido";
    }else{
        $sangria = \MySql::conectar()->prepare("INSERT INTO `tb_produtos` (`id`, `nome`, `preco`, `codigo`, `por_peso`,`codigo_id`) VALUES (NULL,?, ?, ?, ?,?);");
        $sangria->execute(array($_POST['nome'],$_POST['preco'],$_POST['codigo'],$_POST['por_peso'],$_POST['codigo_id']));
    }


    

?>

<?php
include('../../MySql.php');
    $codigo = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
    $codigo->execute(array($_POST['codigo']));
    $codigo = $codigo->fetch();
    $codigo_id = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo_id` = ?");
    $codigo_id->execute(array($_POST['codigo_id']));
    $codigo_id = $codigo_id->fetch();
    print_r($codigo);
    print_r($codigo_id);
    if(!empty($codigo)){
        echo "Codigo_barras_repetido";
    }elseif (!empty($codigo_id)) {
        echo "Codigo_repetido";
    }else{
        $preco = str_replace(",",".",$_POST["preco"]);
        $arrayPrecos = '{"Mix Salgados Ltda": "'.$preco.' ", "Mix Salgados Prainha Ltda": "'.$preco.' ", "Mix Salgados Variados Ltda": "'.$preco.' " }';
        $sangria = \MySql::conectar()->prepare("INSERT INTO `tb_produtos` (`id`, `nome`, `preco`, `codigo`, `por_peso`, `codigo_id`, `vendido`, `ncm`, `cod_grp_financeiro`, `cst_icms`, `icms`, `cst_pis_cofins`,`json_precos`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
        $sangria->execute(array($_POST['nome'],$_POST['preco'],$_POST['codigo'],$_POST['por_peso'],$_POST['codigo_id'],1,$_POST["ncm"],0,$_POST["cst_icms"],$_POST["icms"],$_POST["cst_pis_cofins"],$arrayPrecos ));
        
    }

?>
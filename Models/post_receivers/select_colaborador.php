
<?php
include('../../MySql.php');
if(isset($_POST['blue_sky'])){
    $infos = \MySql::conectar()->prepare("SELECT caixa FROM `tb_colaboradores` WHERE `codigo` =  ?
    ");
    $infos->execute(array($_POST['colaborador']));
    $infos = $infos->fetch();
    if(!empty($infos)){
        echo $infos['caixa'];
    }
}else{
    $infos = \MySql::conectar()->prepare("SELECT nome FROM `tb_colaboradores` WHERE `codigo` LIKE  ?
");
$infos->execute(array($_POST['colaborador'].'%'));
$infos = $infos->fetch();
if(!empty($infos)){
    echo $infos['nome'];
}
}


            ?>

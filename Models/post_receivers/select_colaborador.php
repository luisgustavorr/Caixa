
<?php
include('../../MySql.php');
$infos = \MySql::conectar()->prepare("SELECT nome FROM `tb_colaboradores` WHERE `codigo` LIKE  ?
");
$infos->execute(array($_POST['colaborador'].'%'));
$infos = $infos->fetch();
if(!empty($infos)){
    echo $infos['nome'];
}

            ?>

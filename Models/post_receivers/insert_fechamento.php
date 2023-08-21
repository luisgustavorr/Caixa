<?php 
include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');
    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_fechamento` (`id`, `dinheiro`, `cartao`, `moeda`, `pix`, `sangria`, `data`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");
    $equip->execute(array($_POST['dinheiro_informadas'],$_POST['cartao_informadas'],$_POST['moedas_informadas'],$_POST['pix_informadas'],$_POST['sangria_informadas'],date("Y-m-d")));
    $equip = $equip->fetch();
    ?>
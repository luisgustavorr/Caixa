<?php 
include('../../MySql.php');

    $row = \MySql::conectar()->prepare("SELECT * FROM `tb_nfe` WHERE `impressa` = 0 AND `caixa` = ?");
    $row->execute(array($_COOKIE["caixa"]));
    $row = $row->fetchAll();
    print_r(json_encode($row));


?>
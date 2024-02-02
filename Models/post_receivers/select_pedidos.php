
<?php 
include('../../MySql.php');
if(isset($_POST['anytime'])){
    $row = \MySql::conectar()->prepare("SELECT * FROM `tb_pedidos` WHERE `entregue` = 0 AND `caixa` = ?");
    $row->execute(array($_COOKIE["caixa"]));
    $row = $row->fetchAll();
    print_r(json_encode($row));
}else{
    $row = \MySql::conectar()->prepare("SELECT * FROM `tb_pedidos` WHERE `data_entrega` <= DATE_ADD(NOW(), INTERVAL 30 MINUTE) AND `entregue` = 0 AND `caixa` = ?");
    $row->execute(array($_COOKIE["caixa"]));
    $row = $row->fetchAll();
    print_r(json_encode($row));
}

?>
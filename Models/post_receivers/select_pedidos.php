
<?php 
include('../../MySql.php');

$row = \MySql::conectar()->prepare("SELECT * FROM `tb_pedidos` WHERE `data_entrega` <= DATE_ADD(NOW(), INTERVAL 30 MINUTE) AND `entregue` = 0;");
$row->execute(array());
$row = $row->fetchAll();
print_r(json_encode($row))
?>
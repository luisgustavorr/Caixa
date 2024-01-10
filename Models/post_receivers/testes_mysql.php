<?php 
include('../../MySql.php');
$colab = \MySql::conectar()->prepare("SET GLOBAL max_connections = 500;");
$colab->execute();
$colab = $colab->fetch();

print_r($colab)
?>
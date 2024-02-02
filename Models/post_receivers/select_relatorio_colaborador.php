<?php 
include('../../MySql.php');


$row = \MySql::conectar()->prepare('SELECT * FROM `tb_vendas` WHERE `colaborador` = ? ');
$row->execute(array($_POST['colaborador']));
$row = $row->fetchAll();
foreach ($row as $key => $value) {
    echo $value['colaborador'];
}
// data = {
//     colaborador:'luis'
//     };
    
//     $.post("Models/post_receivers/select_relatorio_colaborador.php", data, function (ret) {
//     console.log(ret)
//     });
?>

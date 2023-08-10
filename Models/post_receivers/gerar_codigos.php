<?php 
require('../../MySql.php');
function generateRandomCode($length = 8) {
    $characters = '0123456789';
    $randomCode = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomCode;
}
do {
    $codigo_id = generateRandomCode(2);
    $result = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo` = ?");
    $result->execute(array($codigo_id));
} while ($result->rowCount() > 0);


// Gerar o código para a coluna "codigo_id"
do {
    $codigo = generateRandomCode(13);
    $row = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo_id` = ?");
    $row->execute(array($codigo));
} while ($row->rowCount() > 0);
$res = [
    "codigo"=> "$codigo",
    "codigo_id"=>"$codigo_id"
];
print_r(json_encode($res));
?>
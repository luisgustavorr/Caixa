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

$ultimo_id = \MySql::conectar()->prepare("SELECT `id` FROM `tb_produtos` ORDER BY `id` DESC LIMIT 1;");
$ultimo_id->execute();
$ultimo_id = $ultimo_id->fetch();


// Gerar o código para a coluna "codigo_id"
do {
    $codigo = generateRandomCode(13);
    $row = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo_id` = ?");
    $row->execute(array($codigo));
} while ($row->rowCount() > 0);
$res = [
    "codigo"=> "$codigo",
    "codigo_id"=>$ultimo_id["id"] + 1
];
print_r(json_encode($res));
?>
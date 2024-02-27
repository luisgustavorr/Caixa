<?php 
require('../../MySql.php');
function IncluiDigito($ean) {
    $digitos = str_split($ean);
    $soma = 0;
    foreach ($digitos as $i => $digito) {
        if (($i % 2) === 0) {
            $soma += $digito * 1;
        } else {
            $soma += $digito * 3;
        }
    }
    $resultado = floor($soma / 10) + 1;
    $resultado *= 10;
    $resultado -= $soma;
    if (($resultado % 10) === 0) {
        $ean = $ean . '0';
    } else {
        $ean = $ean . $resultado;
    }
    return $ean;
}
// echo IncluiDigito(789100031550);
$cnpj = \MySql::conectar()->prepare("SELECT CNPJ FROM `tb_caixas` WHERE caixa = 'Mix Salgados Ltda';");
$cnpj->execute();
$cnpj = $cnpj->fetch();
$cnpjArray = str_split($cnpj[0]);
$primeiros5Digitos = $cnpjArray[0].$cnpjArray[1].$cnpjArray[2].$cnpjArray[3].$cnpjArray[4];


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
    $codigo = generateRandomCode(4);
    $codigo_barras = "789".$primeiros5Digitos.$codigo;
    $codigo_barras =IncluiDigito($codigo_barras);
    $row = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `codigo_id` = ?");
    $row->execute(array($codigo));
} while ($row->rowCount() > 0);
$res = [
    "codigo"=> "$codigo_barras",
    "codigo_id"=>$codigo
];

print_r(json_encode($res));
?>
GERAL_200_ROSQUINHA DA VOVO_200_5,9_5,90_UNIDAD   _3_""_0_0_""_""_""_N_0_1_100_""_0_0_0_0_0_0_0_0_0_0_G_""_""_""_5,9_0_5,9_0_0_0_0_0_0_0

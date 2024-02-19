<?php 
require __DIR__ . '/../../vendor/autoload.php';
include('../../MySql.php');


//use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
 use Mike42\Escpos\PrintConnectors\FilePrintConnector;

use NFePHP\POS\DanfcePos;

require_once "../../vendor/autoload.php";

$colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$colab->execute(array(3));
$colab = $colab->fetch();

$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$caixa->execute(array($colab['caixa']));
$caixa = $caixa->fetch();

$impressora = $caixa['impressora'];
$data ="2024-01-25-08-36-38";
[$ano, $mes, $dia, $hora, $minuto, $segundos] = explode('-', $data);

$nomeArquivo  = 'C:/Users/Public/Documents/NotasFiscais/xml/' . $ano . '/' . $mes . '/' . $dia . '/' . $data . '.xml' ;

// Conectar à impressora térmica
try {
    //$connector = new WindowsPrintConnector($impressora);
     $connector = new  FilePrintConnector("./tmp/test.bin");
    
} catch (\Exception $ex) {
    die('Não foi possível conectar com a impressora.');
}

// Inicializar DanfcePos
$danfcepos = new DanfcePos($connector);

// Carregar logo da empresa
$logopath = realpath(__DIR__ . '/../../img/Logo mix.png'); // Impressa no início da DANFCe
$danfcepos->logo($logopath);

// Carregar NFCe
$xmlpath = $nomeArquivo; // Também poderia ser o conteúdo do XML, no lugar do path
$danfcepos->loadNFCe($xmlpath);

// Imprimir
$danfcepos->imprimir();

echo 'Sucesso!';
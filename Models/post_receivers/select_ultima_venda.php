<?php 
include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
try{
    $colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$colab->execute(array($_COOKIE['last_codigo_colaborador']));
$colab = $colab->fetch();

$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$caixa->execute(array($colab['caixa']));
$caixa = $caixa->fetch();
    @$connector = new WindowsPrintConnector(dest:$caixa['impressora']);

@$printer = new Printer($connector);
$printer -> setTextSize(2, 2);
$printer->setLineSpacing(50);

@$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
@$printer->text("CUPOM DE VENDA\n");


@$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
if(isset($_COOKIE['last_codigo_colaborador'])){
    $data_ultima_venda = \MySql::conectar()->prepare("SELECT `tb_vendas`.`data` FROM `tb_vendas`  INNER JOIN `tb_colaboradores` ON `tb_vendas`.`colaborador` = `tb_colaboradores`.`codigo` WHERE `tb_vendas`.`caixa` = `tb_colaboradores`.`caixa` AND `tb_colaboradores`.`codigo` = ? ORDER BY `data` desc LIMIT 1;");
    $data_ultima_venda->execute(array($_COOKIE['last_codigo_colaborador']));
    $data_ultima_venda = $data_ultima_venda->fetch();
list($dataCompra, $horaCompra) = explode(' ', $data_ultima_venda['data']);

@$printer->text("Data da compra: ".$dataCompra."\n");
@$printer->text("Horário da compra: ".$horaCompra."\n");

    $vendas_com_ultima_data = \MySql::conectar()->prepare("SELECT * FROM `tb_vendas`  INNER JOIN `tb_colaboradores` ON `tb_vendas`.`colaborador` = `tb_colaboradores`.`codigo` INNER JOIN `tb_produtos` ON `tb_produtos`.`id` = `tb_vendas`.`produto` WHERE `tb_vendas`.`caixa` = `tb_colaboradores`.`caixa` AND `tb_colaboradores`.`codigo` = ? AND `data`=? ORDER BY `data` ");
    $vendas_com_ultima_data->execute(array($_COOKIE['last_codigo_colaborador'],$data_ultima_venda['data']));
    $vendas_com_ultima_data = $vendas_com_ultima_data->fetchAll();

    $valor_compra_total = 0;
    foreach ($vendas_com_ultima_data as $key => $value) {
        $valor_compra_total = $valor_compra_total + $value['valor'];
        $quantidade = $value['valor']/$value['preco'];
        echo"Produto: ". $value['nome'] ." Quantidade: ". $quantidade ." Valor: ". number_format($value['valor'],2,',','.');
        @$printer->text("Produto:" . $value['nome'] ."\n");
        @$printer->text("Quantidade:" . $quantidade ."\n");
        @$printer->text(" Valor: " . number_format($value['valor'],2,',','.')."\n");


    $printer->text("-----------------------------------------\n");
       
    }
    @$printer->text("Valor total:R$".number_format($valor_compra_total,2,',','.')."\n");
    $printer->text("-----------------------------------------\n");
    $printer->cut();
    $printer->close();
  }
}catch(Exception $e){
    echo "Erro: ".$e."<br>";
}
  
?>
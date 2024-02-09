<?php
include('../../MySql.php');

require __DIR__ . '/../../vendor/autoload.php';

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
$cookieteste = 3;

try {


    $colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
    $colab->execute(array($cookieteste));
    $colab = $colab->fetch();
    // print_r($colab );
    $caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` INNER JOIN `tb_caixas` ON  `tb_caixas`.`caixa` = `tb_equipamentos`.`caixa` WHERE `tb_equipamentos`.`caixa` = ?");
    $caixa->execute(array($colab['caixa']));
    $caixa = $caixa->fetch();
    $select_last_venda = \MySql::conectar()->prepare("SELECT * FROM `tb_vendas` WHERE `pedido_id` = 0 AND `caixa` = ?
    ORDER BY `tb_vendas`.`id` DESC
    LIMIT 1;");
        $select_last_venda->execute(array($caixa["caixa"]));
        $select_last_venda = $select_last_venda->fetch();
        $select_nfce = \MySql::conectar()->prepare("SELECT * FROM `tb_nfe` WHERE data_venda = ?");
        $select_nfce->execute(array($select_last_venda["data"]));
        $select_nfce = $select_nfce->fetch();
        print_r($select_nfce);
    $infoEnd = json_decode(file_get_contents("https://brasilapi.com.br/api/cep/v1/" . $caixa['CEP']), true);

    $arr = [
        "atualizacao" => date('Y-m-d h:i:s'),
        "tpAmb" => 2,
        "razaosocial" => $caixa["caixa"],
        "cnpj" => $caixa["CNPJ"] . "", // PRECISA SER VÁLIDO
        "ie" => $caixa["IE"] . "", // PRECISA SER VÁLIDO
        "siglaUF" => $infoEnd["state"],
        "schemes" => "PL_009_V4",
        "versao" => '4.00',
        "tokenIBPT" => "AAAAAAA",
        "CSC" => $caixa["CSC"],
        "CSCid" => $caixa["CSCid"],
        "aProxyConf" => [
            "proxyIp" => "",
            "proxyPort" => "",
            "proxyUser" => "",
            "proxyPass" => ""
        ]
    ];
    $configJson = json_encode($arr);
    $path = "../../certificados/" . strtoupper($infoEnd["street"]) . "/";
    $diretorio = scandir($path);
    $arquivo = $diretorio[2];
    $caminhoCertificado = $path . $arquivo;
    $senha_certificado = "123456";
    if($select_last_venda["forma_pagamento"] == "Dinheiro"){
        $update_valor_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_no_caixa` = `valor_no_caixa` - ?, `valor_atual` = `valor_atual`-? WHERE `tb_caixas`.`caixa` = ? ");
        $update_valor_caixa->execute(array($select_last_venda["valor"],$select_last_venda["valor"],$_COOKIE["caixa"]));
    }else{
        $update_valor_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET  `valor_atual` = `valor_atual`-?WHERE `tb_caixas`.`caixa` = ? ");
        $update_valor_caixa->execute(array($select_last_venda["valor"],$_COOKIE["caixa"]));
    }
        $equip = \MySql::conectar()->prepare("DELETE FROM `tb_vendas` WHERE `pedido_id` = 0 AND `caixa` = ?
        ORDER BY `tb_vendas`.`id` DESC
        LIMIT 1;");
        $equip->execute(array($_COOKIE["caixa"]));
    if ($arquivo == "MIX SALGADOS VARIADOS LTDA50070086000105 - Senha Carol@22.pfx") {

        $senha_certificado = "Carol@22";
    }
    // echo $caminhoCertificado;
    $pfxcontent  = file_get_contents($caminhoCertificado);

    $certificate = Certificate::readPfx($pfxcontent, $senha_certificado);
    $tools = new Tools($configJson, $certificate);
    $tools->model('65');

    $chave = $select_nfce["chaveNFe"];
    $xJust = 'Cliente cancelou a compra.';
    $nProt = $select_nfce["protocolo"];
    $response = $tools->sefazCancela($chave, $xJust, $nProt);

    //você pode padronizar os dados de retorno atraves da classe abaixo
    //de forma a facilitar a extração dos dados do XML
    //NOTA: mas lembre-se que esse XML muitas vezes será necessário, 
    //      quando houver a necessidade de protocolos
    $stdCl = new Standardize($response);
    //nesse caso $std irá conter uma representação em stdClass do XML
    $std = $stdCl->toStd();
    //nesse caso o $arr irá conter uma representação em array do XML
    $arr = $stdCl->toArray();
    //nesse caso o $json irá conter uma representação em JSON do XML
    $json = $stdCl->toJson();

    //verifique se o evento foi processado
    if ($std->cStat != 128) {
        print_r($std);
        //houve alguma falha e o evento não foi processado
        //TRATAR
    } else {
        $cStat = $std->retEvento->infEvento->cStat;
        if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
            //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
            $xml = Complements::toAuthorize($tools->lastRequest, $response);
            //grave o XML protocolado 
            print_r($xml);
        } else {
            //houve alguma falha no evento 
            //TRATAR
            print_r($std);
        }
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}

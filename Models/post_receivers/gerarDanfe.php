<?php

error_reporting(E_ERROR);
ini_set('display_errors', 'On');
require __DIR__ . '/../../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');

use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapFake;
use NFePHP\DA\NFe\Danfce;


$arr = [
    "atualizacao" => date('Y-m-d h:i:s'),
    "tpAmb" => 2,
    "razaosocial" => "MIX SALGADOS PRAINHA LTDA ",
    "cnpj" => "52151256000101", // PRECISA SER VÁLIDO
    "ie" => '0047125780008', // PRECISA SER VÁLIDO
    "siglaUF" => "MG",
    "schemes" => "PL_009_V4",
    "versao" => '4.00',
    "tokenIBPT" => "AAAAAAA",
    "CSC" => "59cf2d8d83fcb5ab88687652a6db9e02",
    "CSCid" => "000001",
    "aProxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];
$configJson = json_encode($arr);
$pfxcontent = file_get_contents('../../certificados/PRAINHA/MIX-SALGADOS-PRAINHA-LTDA_52151256000101.pfx');
$data_emissao = date('Y-m-d-H-i-s');
$arrayRetorno['data'] = $data_emissao;

function criarArquivoNFe($data_atual, $tipo, $arquivo)
{

    [$ano, $mes, $dia, $hora, $minuto, $segundos] = explode('-', $data_atual);
    if (file_exists('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano)) {

        if (file_exists('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes)) {
            if (file_exists('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia)) {
                file_put_contents('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia . '/' . $data_atual . '.' . $tipo, $arquivo);
            } else {
                mkdir('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia, 0777, true);
                file_put_contents('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia . '/' . $data_atual . '.' . $tipo, $arquivo);
            }
        } else {
            mkdir('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia, 0777, true);
            file_put_contents('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia . '/' . $data_atual . '.' . $tipo, $arquivo);
        }
    } else {
        mkdir('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia, 0777, true);
        file_put_contents('C:/Users/Public/Documents/NotasFiscais/' . $tipo . '/' . $ano . '/' . $mes . '/' . $dia . '/' . $data_atual . '.' . $tipo, $arquivo);
    }
};


$tools = new Tools($configJson, Certificate::readPfx($pfxcontent, '123456'));
//$tools->disableCertValidation(true); //tem que desabilitar
$tools->model('65');

try {

    $make = new Make();


    //infNFe OBRIGATÓRIA
    $std = new \stdClass();
    $std->Id = '';
    $std->versao = '4.00';
    $infNFe = $make->taginfNFe($std);

    //ide OBRIGATÓRIA
    $std = new \stdClass();
    $std->cUF = 31;
    $std->cNF = '83701267';
    $std->natOp = 'VENDA CONSUMIDOR';
    $std->mod = 65;
    $std->serie = 1;
    $std->nNF = 100;
    $std->dhEmi = (new \DateTime())->format('Y-m-d\TH:i:sP');
    $std->dhSaiEnt = null;
    $std->tpNF = 1;
    $std->idDest = 1;
    $std->cMunFG = 3133808;
    $std->tpImp = 5;
    $std->tpEmis = 1;
    $std->cDV = 2;
    $std->tpAmb = 2;
    $std->finNFe = 1;
    $std->indFinal = 1;
    $std->indPres = 1;
    $std->procEmi = 0;
    $std->verProc = '4.13';
    $std->dhCont = null;
    $std->xJust = null;
    $ide = $make->tagIde($std);

    //emit OBRIGATÓRIA
    $std = new \stdClass();
    $std->xNome = 'MIX SALGADOS PRAINHA LTDA ';
    $std->xFant = 'RAZAO';
    $std->IE = '0047125780008';
    $std->IEST = null;
    //$std->IM = '95095870';
    $std->CNAE = '5611203';
    $std->CRT = 1;
    $std->CNPJ = '52151256000101';
    //$std->CPF = '12345678901'; //NÃO PASSE TAGS QUE NÃO EXISTEM NO CASO
    $emit = $make->tagemit($std);

    //enderEmit OBRIGATÓRIA
    $std = new \stdClass();
    $std->xLgr = 'RUA ANTÔNIO CARCEREIRO';
    $std->nro = '16';
    $std->xCpl = 'n';
    $std->xBairro = 'VARZEA DA OLARIA';
    $std->cMun = 3133808;
    $std->xMun = 'ITAÚNA';
    $std->UF = 'MG';
    $std->CEP = '35680121';
    $std->cPais = 1058;
    $std->xPais = 'Brasil';
    $std->fone = '55555555';
    $ret = $make->tagenderemit($std);



    //prod OBRIGATÓRIA
    $std = new \stdClass();
    $std->item = 1;
    $std->cProd = '1111';
    $std->cEAN = "SEM GTIN";
    $std->xProd = 'CAMISETA REGATA GG';
    $std->NCM = 61052000;
    //$std->cBenef = 'ab222222';
    $std->EXTIPI = '';
    $std->CFOP = 5101;
    $std->uCom = 'UNID';
    $std->qCom = 1;
    $std->vUnCom = 100.00;
    $std->vProd = 100.00;
    $std->cEANTrib = "SEM GTIN"; //'6361425485451';
    $std->uTrib = 'UNID';
    $std->qTrib = 1;
    $std->vUnTrib = 100.00;
    //$std->vFrete = 0.00;
    //$std->vSeg = 0;
    //$std->vDesc = 0;
    //$std->vOutro = 0;
    $std->indTot = 1;
    //$std->xPed = '12345';
    //$std->nItemPed = 1;
    //$std->nFCI = '12345678-1234-1234-1234-123456789012';
    $prod = $make->tagprod($std);

    $tag = new \stdClass();
    $tag->item = 1;
    $tag->infAdProd = 'DE POLIESTER 100%';
    $make->taginfAdProd($tag);

    //Imposto 
    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->vTotTrib = 25.00;
    $make->tagimposto($std);

    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->orig = 0;
    $std->CSOSN = '102';
    $std->pCredSN = 0.00;
    $std->vCredICMSSN = 0.00;
    $std->modBCST = null;
    $std->pMVAST = null;
    $std->pRedBCST = null;
    $std->vBCST = null;
    $std->pICMSST = null;
    $std->vICMSST = null;
    $std->vBCFCPST = null; //incluso no layout 4.00
    $std->pFCPST = null; //incluso no layout 4.00
    $std->vFCPST = null; //incluso no layout 4.00
    $std->vBCSTRet = null;
    $std->pST = null;
    $std->vICMSSTRet = null;
    $std->vBCFCPSTRet = null; //incluso no layout 4.00
    $std->pFCPSTRet = null; //incluso no layout 4.00
    $std->vFCPSTRet = null; //incluso no layout 4.00
    $std->modBC = null;
    $std->vBC = null;
    $std->pRedBC = null;
    $std->pICMS = null;
    $std->vICMS = null;
    $std->pRedBCEfet = null;
    $std->vBCEfet = null;
    $std->pICMSEfet = null;
    $std->vICMSEfet = null;
    $std->vICMSSubstituto = null;
    $make->tagimposto($std);

    //PIS
    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->CST = '99';
    //$std->vBC = 1200;
    //$std->pPIS = 0;
    $std->vPIS = 0.00;
    $std->qBCProd = 0;
    $std->vAliqProd = 0;
    $pis = $make->tagPIS($std);

    //COFINS
    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->CST = '99';
    $std->vBC = null;
    $std->pCOFINS = null;
    $std->vCOFINS = 0.00;
    $std->qBCProd = 0;
    $std->vAliqProd = 0;
    $make->tagCOFINS($std);

    //icmstot OBRIGATÓRIA
    $std = new \stdClass();
    //$std->vBC = 100;
    //$std->vICMS = 0;
    //$std->vICMSDeson = 0;
    //$std->vFCPUFDest = 0;
    //$std->vICMSUFDest = 0;
    //$std->vICMSUFRemet = 0;
    //$std->vFCP = 0;
    //$std->vBCST = 0;
    //$std->vST = 0;
    //$std->vFCPST = 0;
    //$std->vFCPSTRet = 0.23;
    //$std->vProd = 2000;
    //$std->vFrete = 100;
    //$std->vSeg = null;
    //$std->vDesc = null;
    //$std->vII = 12;
    //$std->vIPI = 23;
    //$std->vIPIDevol = 9;
    //$std->vPIS = 6;
    //$std->vCOFINS = 25;
    //$std->vOutro = null;
    //$std->vNF = 2345.83;
    //$std->vTotTrib = 798.12;
    $icmstot = $make->tagicmstot($std);

    //transp OBRIGATÓRIA
    $std = new \stdClass();
    $std->modFrete = 9;
    $transp = $make->tagtransp($std);


    //pag OBRIGATÓRIA
    $std = new \stdClass();
    $std->vTroco = 0;
    $pag = $make->tagpag($std);

    //detPag OBRIGATÓRIA
    $std = new \stdClass();
    $std->indPag = 1;
    $std->tPag = '01';
    $std->vPag = 100.00;
    $detpag = $make->tagdetpag($std);

    //infadic
    $std = new \stdClass();
    $std->infAdFisco = '';
    $std->infCpl = '';
    $info = $make->taginfadic($std);

    $std = new stdClass();
    $std->CNPJ = '52151256000101'; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
    $std->xContato = 'Fulano de Tal'; //Nome da pessoa a ser contatada
    $std->email = 'fulano@soft.com.br'; //E-mail da pessoa jurídica a ser contatada
    $std->fone = '1155551122'; //Telefone da pessoa jurídica/física a ser contatada
    //$std->CSRT = 'G8063VRTNDMO886SFNK5LDUDEI24XJ22YIPO'; //Código de Segurança do Responsável Técnico
    //$std->idCSRT = '01'; //Identificador do CSRT
    $make->taginfRespTec($std);

    $make->monta();
    $xml = $make->getXML();


    $xml = $tools->signNFe($xml);

    header('Content-Type: application/xml; charset=utf-8');
    echo $xml;
    try {
        $logo = file_get_contents(realpath(__DIR__ . '/../../img/Logo mix.png'));
        $danfce = new Danfce($xml);
        $danfce->debugMode(true); //seta modo debug, deve ser false em produção
        $danfce->setPaperWidth(80); //seta a largura do papel em mm max=80 e min=58
        $danfce->setMargins(2); //seta as margens
        $danfce->setDefaultFont('arial'); //altera o font pode ser 'times' ou 'arial'
        $danfce->setOffLineDoublePrint(true); //ativa ou desativa a impressão conjunta das via do consumidor e da via do estabelecimento qnado a nfce for emitida em contingência OFFLINE
        //$danfce->setPrintResume(true); //ativa ou desativa a impressao apenas do resumo
        //$danfce->setViaEstabelecimento(); //altera a via do consumidor para a via do estabelecimento, quando a NFCe for emitida em contingência OFFLINE
        //$danfce->setAsCanceled(); //força marcar nfce como cancelada
        $danfce->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webnfe.com.br');
        $pdf = $danfce->render($logo);
        criarArquivoNFe($data_emissao, 'pdf', $pdf);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    try {
        $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
        $resp = $tools->sefazEnviaLote([$xml], $idLote);

        $st = new NFePHP\NFe\Common\Standardize();
        $std = $st->toStd($resp);
        if ($std->cStat != 103) {
            //erro registrar e voltar
            print_r($std);
            exit();
        }
        $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota
    } catch (\Exception $e) {
        //aqui você trata possiveis exceptions do envio
        exit($e->getMessage());
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
<?php
include('../../MySql.php');

error_reporting(E_ERROR);
ini_set('display_errors', 'On');
require __DIR__ . '/../../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');

use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\Common\Certificate;
use NFePHP\DA\NFe\Danfce;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;


$cookieteste = 3;
$arrayRetorno = [
    'retorno' => [],

];

$colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$colab->execute(array($cookieteste));
$colab = $colab->fetch();
// print_r($colab );
$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` INNER JOIN `tb_caixas` ON  `tb_caixas`.`caixa` = `tb_equipamentos`.`caixa` WHERE `tb_equipamentos`.`caixa` = ?");
$caixa->execute(array($colab['caixa']));
$caixa = $caixa->fetch();

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

$senha_certificado = "123456";
if ($arquivo == "MIX SALGADOS VARIADOS LTDA50070086000105 - Senha Carol@22.pfx") {

    $senha_certificado = "Carol@22";
}
$caminhoCertificado = $path . $arquivo;

// echo $caminhoCertificado;
$pfxcontent  = file_get_contents($caminhoCertificado);

$data_emissao = date('Y-m-d-H-i-s');
$arrayRetorno['data'] = $data_emissao;
// print_r(json_encode($arrayRetorno));

if (!isset($_POST['data_venda'])) {
    $data_ultima_venda = \MySql::conectar()->prepare("SELECT `tb_vendas`.`data` FROM `tb_vendas` WHERE `tb_vendas`.`colaborador` = ? AND pedido_id =0 ORDER BY `id` desc LIMIT 1;");
    $data_ultima_venda->execute(array($cookieteste));
    $data_ultima_venda = $data_ultima_venda->fetch();
    $data_ultima_venda = $data_ultima_venda["data"];
} else {
    $data_ultima_venda = $_POST['data_venda'];
}


list($dataCompra, $horaCompra) = explode(' ', $data_ultima_venda);

$vendas_com_ultima_data = \MySql::conectar()->prepare("SELECT `tb_vendas`.venda_dividida_id,`tb_vendas`.valor,`tb_vendas`.quantidade_produto ,`tb_vendas`.forma_pagamento,`tb_vendas`.troco,tb_produtos.*  FROM `tb_vendas`  INNER JOIN `tb_colaboradores` ON `tb_vendas`.`colaborador` = `tb_colaboradores`.`codigo` INNER JOIN `tb_produtos` ON `tb_produtos`.`id` = `tb_vendas`.`produto` WHERE `tb_vendas`.`caixa` = `tb_colaboradores`.`caixa` AND `tb_colaboradores`.`codigo` = ? AND `tb_vendas`.`data`=? ORDER BY `data` ");
$vendas_com_ultima_data->execute(array($cookieteste, $data_ultima_venda));
$vendas_com_ultima_data = $vendas_com_ultima_data->fetchAll();
// print_r($vendas_com_ultima_data);

$select_ultima_nfe = \MySql::conectar()->prepare("SELECT * FROM `tb_nfe` WHERE impressa != 0 ORDER by numero_nfe DESC LIMIT 1;");
$select_ultima_nfe->execute();
$select_ultima_nfe = $select_ultima_nfe->fetch();
$n_nfe = $select_ultima_nfe["numero_nfe"] + 1;

$select_nfe = \MySql::conectar()->prepare("SELECT * FROM `tb_nfe` WHERE data_venda =  ? AND caixa = ?");
$select_nfe->execute(array($data_ultima_venda, $caixa["caixa"]));
$select_nfe = $select_nfe->fetchAll();


//    if(count($select_nfe) !=0){
//          $data_formatada = date("Y-m-d-H-i-s", strtotime($select_nfe[0]['data']));
//           $arrayRetorno['data'] = $data_formatada;
//       $arrayRetorno["retornoRecibo"] = "";
//        exit;
//     }

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


$tools = new Tools($configJson, Certificate::readPfx($pfxcontent, $senha_certificado));
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
    $std->serie = $n_nfe;
    // $n_nfe
    $std->nNF = 1000;
    $std->dhEmi = (new \DateTime())->format('Y-m-d\TH:i:sP');
    $std->dhSaiEnt = null;
    $std->tpNF = 1;
    $std->idDest = 1;
    $std->cMunFG = 3133808;
    $std->tpImp = 5;
    $std->tpEmis = 1;
    $std->cDV = 2;
    $std->tpAmb = 2; // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
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
    $std->xNome = $caixa["caixa"];
    $std->xFant = $caixa["caixa"];
    $std->IE = $caixa["IE"] . "";
    $std->IEST = null;
    //$std->IM = '95095870';
    $std->CNAE = '5611203';
    $std->CRT = 1;
    $std->CNPJ = $caixa["CNPJ"] . "";
    //$std->CPF = '12345678901'; //NÃO PASSE TAGS QUE NÃO EXISTEM NO CASO
    $emit = $make->tagemit($std);


    //enderEmit OBRIGATÓRIA
    $std = new \stdClass();
    $std->xLgr = $infoEnd["street"];
    $std->nro = $caixa["numero"];
    $std->xCpl = ' ';
    $std->xBairro = $infoEnd["neighborhood"];
    $std->cMun = 3133808;
    $std->xMun = $infoEnd["city"];
    $std->UF = $infoEnd["state"];
    $std->CEP = $caixa["CEP"];
    $std->cPais = 1058;
    $std->xPais = 'Brasil';
    $std->fone = '3799510441';
    $ret = $make->tagenderemit($std);
    $errors = $make->getErrors();
    // print_r($vendas_com_ultima_data);
    if (isset($_POST["nome_cliente"]) and isset($_POST["cpf_nfe"])) {
        if ($_POST["nome_cliente"] != "" and $_POST["cpf_nfe"] != "") {
            $std = new \stdClass();
            $std->xNome = $_POST["nome_cliente"];
            $std->CPF = str_replace(".", "", str_replace("-", "", $_POST["cpf_nfe"]));
            $std->indIEDest = 9;
            $dest = $make->tagdest($std);
            $arrayRetorno["retorno"] = "nova criada";
        }
    }
    $valor_total_produtos = 0;
    $valor_total_icms = 0;
    $venda_dividida = false;
    foreach ($vendas_com_ultima_data as $key => $value) {
        if ($value["venda_dividida_id"] != 0) {
            $venda_dividida = true;
        }
        $valor_produto = $value['valor'];
        $quantidade = $valor_produto / str_replace(',', '.', $value["preco"]);
        // echo $quantidade;
        $valor_total_produtos = $valor_produto + $valor_total_produtos;
        // print_r($vendas_com_ultima_data);



        if ($value['por_peso'] == 1) {
            $UN = 'KG';
        } else {
            $UN = 'UNID';
        }
        $pICMS = $value['icms'];

        // + ($IPI / 100 * $valor_produto) + ($PIS / 100 * $valor_produto) + ($COFINS / 100 * $valor_produto)
        $valor_total_tributos = ($pICMS / 100 * $valor_produto);
        $item = $key + 1;

        $valorICMS = number_format($valor_produto * ($pICMS / 100), 2, '.', '');
        $valor_total_icms = $valorICMS + $valor_total_icms;
        //prod OBRIGATÓRIA
        $std = new \stdClass();
        $std->item = $item;
        $std->cProd =  $value['codigo_id'];
        $std->cEAN = "SEM GTIN";
        $std->xProd =  $value['nome'];
        $std->NCM = str_replace('.', '', $value['ncm']);
        //$std->cBenef = 'ab222222';
        $std->EXTIPI = '';
        $std->CFOP = 5101;
        $std->uCom = $UN;
        $std->qCom = $quantidade;
        $std->vUnCom =  str_replace(',', '.', $value["preco"]);
        $std->vProd = $valor_produto;
        $std->cEANTrib = "SEM GTIN"; //'6361425485451';
        $std->uTrib =  $UN;
        $std->qTrib = $quantidade;
        $std->vUnTrib = str_replace(',', '.', $value["preco"]);
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
        $tag->item = $item;
        if ($venda_dividida) {
            $tag->infAdProd = 'Valor pago com duas formas de pagamento.';
        } else {
            $tag->infAdProd = 'Valor pago integralmente com uma única forma de pagamento.';
        }
        $make->taginfAdProd($tag);

        //Imposto 
        $std = new stdClass();
        $std->item = $item; //item da NFe
        $std->vTotTrib = $valor_total_tributos;
        $make->tagimposto($std);

        $std = new stdClass();
        $std->item = $item; //item da NFe
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
        $std->vBC = $valor_produto;
        $std->pRedBC = null;
        $std->pICMS = $pICMS;
        $std->vICMS = $valorICMS;
        $std->pRedBCEfet = null;
        $std->vBCEfet = null;
        $std->pICMSEfet = null;
        $std->vICMSEfet = null;
        $std->vICMSSubstituto = null;
        $make->tagICMSSN($std);
    }
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
    $std->vTroco = $vendas_com_ultima_data[0]["troco"];
    $pag = $make->tagpag($std);

    //detPag OBRIGATÓRIA


    $arrayFormaPagamento = [
        "01" => "Dinheiro",
        "03" => "Cartão Crédito",
        "04" => "Cartão Débito",
        "17" => "Pix",
    ];
    $arrayFormaPagamento = array_flip($arrayFormaPagamento);
    $std = new \stdClass();
    if ($vendas_com_ultima_data[0]["forma_pagamento"] == 'Cartão Crédito') {
        $indPag = 2;
    } else {
        $indPag = 1;
    }
    if ($venda_dividida) {
        $tPag = 99;
    } else {
        $tPag = $arrayFormaPagamento[$vendas_com_ultima_data[0]["forma_pagamento"]];
    }
    $std->tPag =  $tPag;
    $std->vPag = $valor_total_produtos +  $vendas_com_ultima_data[0]["troco"];
    $std->tpIntegra = 2;
    $detpag = $make->tagdetpag($std);

    //infadic
    $std = new \stdClass();
    $std->infAdFisco = '';
    $std->infCpl = '';
    $info = $make->taginfadic($std);

    // $std = new stdClass();
    // $std->CNPJ = $caixa["CNPJ"]; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
    // $std->xContato = 'Fulano de Tal'; //Nome da pessoa a ser contatada
    // $std->email = 'fulano@soft.com.br'; //E-mail da pessoa jurídica a ser contatada
    // $std->fone = '1155551122'; //Telefone da pessoa jurídica/física a ser contatada
    // //$std->CSRT = 'G8063VRTNDMO886SFNK5LDUDEI24XJ22YIPO'; //Código de Segurança do Responsável Técnico
    // //$std->idCSRT = '01'; //Identificador do CSRT
    // $make->taginfRespTec($std);

    $make->monta();
    $xml = $make->getXML();


    $xml = $tools->signNFe($xml);
 

    // echo $xml;


    try {
        $idLote = substr(str_replace('.','',$caixa["CNPJ"]),0,3).date('ymdHis');
        $resp = $tools->sefazEnviaLote([$xml], $idLote,1);

        $st = new NFePHP\NFe\Common\Standardize();
        $xmlResposta = $resp;
        $std = $st->toStd($resp);
        
            //erro registrar e voltar
            $stdCl = new Standardize($resp);
            $arr = $stdCl->toArray();
     
            $nProt = $arr["protNFe"]["infProt"]["nProt"];
            $chNFe = $arr["protNFe"]["infProt"]["chNFe"];

            if (!isset($_POST['data_venda'])) {
                $insert_nfe = \MySql::conectar()->prepare("INSERT INTO `tb_nfe` (`id`, `data`, `data_venda`, `numero_nfe`,`impressa`,`caixa`,`protocolo`,`chaveNFe`) VALUES (NULL, ?, ?, ?,?,?,?,?);");
                $insert_nfe->execute(array($data_emissao, $data_ultima_venda, $n_nfe, 1, $caixa["caixa"], $nProt, $chNFe));
            } else {
                $update = \MySql::conectar()->prepare("UPDATE tb_nfe SET impressa = 1 WHERE data_venda = ? AND impressa = 0 AND caixa = ? ");
                $update->execute(array($data_ultima_venda, $caixa["caixa"]));
            }
            $arrayRetorno["retornoRecibo"] = $std;

      
        

        $req = $xml;
        $res = $xmlResposta;

        try {
            $xml = Complements::toAuthorize($req, $res);
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
            criarArquivoNFe($data_emissao, 'xml', $xml);
            print_r(json_encode($arrayRetorno));
        } catch (\Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
    } catch (\Exception $e) {
        //aqui você trata possiveis exceptions do envio
        exit($e->getMessage());
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}

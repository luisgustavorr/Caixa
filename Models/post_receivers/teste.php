<?php
require __DIR__ . '/../../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');


use NFePHP\NFe\Make;
use NFePHP\DA\NFe\Danfe;

$nfe = new Make();
$std = new \stdClass();

$std->versao = '4.00';
$std->Id = null;
$std->pk_nItem = '';
$nfe->taginfNFe($std);

$std = new \stdClass();
$std->cUF = 31; //coloque um código real e válido
$std->cNF = '000001';
$std->natOp = 'VENDA';
$std->mod = 55;
$std->serie = 1;
$std->nNF = 10;
$std->dhEmi = date('Y-m-d\TH:i:sP');
$std->dhSaiEnt = date('Y-m-d\TH:i:sP');
$std->tpNF = 1;
$std->idDest = 1;
$std->cMunFG = 3133808; //Código de município precisa ser válido
$std->tpImp = 1;
$std->tpEmis = 1;
$std->cDV = 2;
$std->tpAmb = 2; // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
$std->finNFe = 1;
$std->indFinal = 1;
$std->indPres = 0;
$std->procEmi = '0';
$std->verProc = 1;
$nfe->tagide($std);

$std = new \stdClass();
$std->xNome = 'Mix Salgados Ltda';
$std->IE = '44288470048';
$std->CRT = 3;
$std->CNPJ = '47767930000139';
$nfe->tagemit($std);

$std = new \stdClass();
$std->xLgr = "Rua Teste";
$std->nro = '16';
$std->xBairro = 'VARZEA DA OLARIA';
$std->cMun = 3133808; //Código de município precisa ser válido e igual o  cMunFG
$std->xMun = 'Itaúna';
$std->UF = 'MG';
$std->CEP = '35680121';
$std->cPais = '1058';
$std->xPais = 'BRASIL';
$nfe->tagenderEmit($std);

$std = new \stdClass();
$std->xNome = 'CONSUMIDOR';
$std->indIEDest = 9;
$std->CPF = '15483790693';

$nfe->tagdest($std);

$std = new \stdClass();
$std->xLgr = "Rua Teste";
$std->nro = '16';
$std->xBairro = 'VARZEA DA OLARIA';
$std->cMun = 3133808; //Código de município precisa ser válido e igual o  cMunFG
$std->xMun = 'Itaúna';
$std->UF = 'MG';
$std->CEP = '35680121';
$std->cPais = '1058';
$std->xPais = 'BRASIL';
$nfe->tagenderDest($std);
$valor_produto = 7.5;
$pICMS = 18;

$std = new \stdClass();
$std->item = 1;
$std->cEAN = 'SEM GTIN';
$std->cEANTrib = 'SEM GTIN';
$std->cProd = '0001';
$std->xProd = 'Produto teste';
$std->NCM = '84669330';
$std->CFOP = '5102';
$std->uCom = 'PÇ';
$std->qCom = '1.0000';
$std->vUnCom = $valor_produto;
$std->vProd = $valor_produto;
$std->uTrib = 'PÇ';
$std->qTrib = '1.0000';
$std->vUnTrib = $valor_produto;
$std->indTot = 1;
$nfe->tagprod($std);

$std = new \stdClass();
$std->item = 1;
$std->vTotTrib = 10.99;
$nfe->tagimposto($std);

$std = new \stdClass();
$std->item = 1;
$std->orig = 0;
$std->CST = '00'; //codigo do icms (CST_ICMS na tabela)
$std->modBC = 1;
$std->vBC = $valor_produto; // valor do produto
$std->pICMS = $pICMS; //porcentagem do icms (ICMS na tabela)
$std->vICMS = 1.35; // valor icms porcentagem X valor do produto
$nfe->tagICMS($std);


$std = new \stdClass();
$std->item = 1;
$std->cEnq = '999';
$std->CST = '50';
$std->vIPI = 0;
$std->vBC = 0;
$std->pIPI = 0;
$nfe->tagIPI($std);

$std = new \stdClass();
$std->item = 1;
$std->CST = '99'; //codigo do PIS (CST_PIS_COFINS na tabela)
$std->vBC = $valor_produto;
$std->pPIS = 0; //PIS DA MIX
$std->vPIS = 0; // valor PIS porcentagem X valor do produto
$nfe->tagPIS($std);

$std = new \stdClass();
$std->item = 1;
$std->vCOFINS =  $valor_produto;
$std->vBC = 0;
$std->pCOFINS = 0;

$nfe->tagCOFINSST($std);

$std = new \stdClass();
$std->item = 1;
$std->CST = '99';
$std->vBC =  $valor_produto;
$std->pCOFINS = 0;
$std->vCOFINS = 0;
$std->qBCProd = 0;
$std->vAliqProd = 0;
$nfe->tagCOFINS($std);

$std = new \stdClass();
$std->vBC = $valor_produto;
$std->vICMS = 1.35;
$std->vICMSDeson = 0.00;
$std->vBCST = 0.00;
$std->vST = 0.00;
$std->vProd = $valor_produto;
$std->vFrete = 0.00;
$std->vSeg = 0.00;
$std->vDesc = 0.00;
$std->vII = 0.00;
$std->vIPI = 0.00;
$std->vPIS = 0.00;
$std->vCOFINS = 0.00;
$std->vOutro = 0.00;
$std->vNF = 7.5;
$std->vTotTrib = 0.00;
$nfe->tagICMSTot($std);

//USAR EM CASO DE ENTREGA 
//9 se nao for entrega e 3 se for
$std = new \stdClass();
$std->modFrete = 9;
$nfe->tagtransp($std);

// $std = new \stdClass();
// $std->item = 1;
// $std->qVol = 2;
// $std->esp = 'caixa';
// $std->marca = 'OLX';
// $std->nVol = '11111';
// $std->pesoL = 10.00;
// $std->pesoB = 11.00;
// $nfe->tagvol($std);

$std = new \stdClass();
$std->nFat = '002';
$std->vOrig = 100;
$std->vLiq = 100;
$nfe->tagfat($std);

//USAR QUANDO FOR PARCELA
// $std = new \stdClass();
// $std->nDup = '001';
// $std->dVenc = date('Y-m-d');
// $std->vDup = 11.03;
// $nfe->tagdup($std);

$std = new \stdClass();
$std->vTroco = 0;
$nfe->tagpag($std);

$std = new \stdClass();
$std->indPag = 0;
$std->tPag = "01";
$std->vPag = 7.5;
$nfe->tagdetPag($std);

$xml = $nfe->getXML();
$caminhoArquivo = './nfe.xml';

// Tenta salvar o XML no arquivo
if (file_put_contents($caminhoArquivo, $xml) !== false) {
    echo 'XML da NFe salvo com sucesso!';
} else {
    echo 'Erro ao salvar o XML da NFe.';
}
// print_r($xml);
$config  = [
    "atualizacao" => date('Y-m-d h:i:s'),
    "tpAmb" => 2,
    "razaosocial" => "Mix Salgados Ltda",
    "cnpj" => "47767930000139", // PRECISA SER VÁLIDO
    "ie" => '44288470048', // PRECISA SER VÁLIDO
    "siglaUF" => "MG",
    "schemes" => "PL_009_V4",
    "versao" => '4.00',
    "tokenIBPT" => "AAAAAAA",
    "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
    "CSCid" => "000002",
    "aProxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];

try {
    $logo = file_get_contents(realpath(__DIR__ . '/../../img/Logo mix.png'));
    $danfe = new Danfe($xml);
    $danfe->exibirTextoFatura = false;
    $danfe->exibirPIS = false;
    $danfe->exibirIcmsInterestadual = false;
    $danfe->exibirValorTributos = false;
    $danfe->descProdInfoComplemento = false;
    $danfe->exibirNumeroItemPedido = false;
    $danfe->setOcultarUnidadeTributavel(true);
    $danfe->obsContShow(false);
    $danfe->printParameters(
        $orientacao = 'P',
        $papel = 'A7',
        $margSup = 2,
        $margEsq = 2
    );
    $danfe->logoParameters($logo, $logoAlign = 'C', $mode_bw = false);
    $danfe->setDefaultFont($font = 'times');
    $danfe->setDefaultDecimalPlaces(4);
    $danfe->debugMode(false);
    $danfe->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webenf.com.br');
    //$danfe->epec('891180004131899', '14/08/2018 11:24:45'); //marca como autorizada por EPEC

    // Caso queira mudar a configuracao padrao de impressao
    /*  $this->printParameters( $orientacao = '', $papel = 'A4', $margSup = 2, $margEsq = 2 ); */
    // Caso queira sempre ocultar a unidade tributável
    /*  $this->setOcultarUnidadeTributavel(true); */
    //Informe o numero DPEC
    /*  $danfe->depecNumber('123456789'); */
    //Configura a posicao da logo
    /*  $danfe->logoParameters($logo, 'C', false);  */
    //Gera o PDF
    $pdf = $danfe->render($logo);
    header('Content-Type: application/pdf');
    if (file_put_contents('./teste.pdf', $pdf) !== false) {
      echo 'Sucesso';
    } else {
        echo 'Erro ao salvar o XML da NFe.';
    }

   
} catch (InvalidArgumentException $e) {
    echo "Ocorreu um erro durante o processamento :" . $e->getMessage();
}

$configJson = json_encode($config);
// $certificadoDigital = file_get_contents('certificado.pfx');

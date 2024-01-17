<?php


include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');

use NFePHP\NFe\Make;
use NFePHP\DA\NFe\Danfe;
use NFePHP\NFe\Complements;


$cookieteste = $_COOKIE['last_codigo_colaborador'];
$colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$colab->execute(array($cookieteste));
$colab = $colab->fetch();

$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` INNER JOIN `tb_caixas` ON  `tb_caixas`.`caixa` = `tb_equipamentos`.`caixa` WHERE `tb_equipamentos`.`caixa` = ?");
$caixa->execute(array($colab['caixa']));
$caixa = $caixa->fetch();
$arrayRetorno = [
    'retorno' => [],

];

if (isset($cookieteste)) {


    $data_emissao = date('Y-m-d-H-i-s');
    $arrayRetorno['data'] = $data_emissao;

    $data_ultima_venda = \MySql::conectar()->prepare("SELECT `tb_vendas`.`data` FROM `tb_vendas` WHERE `tb_vendas`.`colaborador` = ? AND pedido_id =0 ORDER BY `id` desc LIMIT 1;");
    $data_ultima_venda->execute(array($cookieteste));
    $data_ultima_venda = $data_ultima_venda->fetch();
    list($dataCompra, $horaCompra) = explode(' ', $data_ultima_venda['data']);

    $vendas_com_ultima_data = \MySql::conectar()->prepare("SELECT `tb_vendas`.valor,`tb_vendas`.quantidade_produto ,tb_produtos.*  FROM `tb_vendas`  INNER JOIN `tb_colaboradores` ON `tb_vendas`.`colaborador` = `tb_colaboradores`.`codigo` INNER JOIN `tb_produtos` ON `tb_produtos`.`id` = `tb_vendas`.`produto` WHERE `tb_vendas`.`caixa` = `tb_colaboradores`.`caixa` AND `tb_colaboradores`.`codigo` = ? AND `tb_vendas`.`data`=? ORDER BY `data` ");
    $vendas_com_ultima_data->execute(array($cookieteste, $data_ultima_venda['data']));
    $vendas_com_ultima_data = $vendas_com_ultima_data->fetchAll();
    $n_nfe = rand(0, 999) + rand(0, 999);

    $select_nfe = \MySql::conectar()->prepare("SELECT * FROM `tb_nfe` WHERE data_venda =  ?");
    $select_nfe->execute(array($data_ultima_venda['data']));
    $select_nfe = $select_nfe->fetchAll();

    if(count($select_nfe) !=0){
         $data_formatada = date("Y-m-d-H-i-s", strtotime($select_nfe[0]['data']));
          $arrayRetorno['data'] = $data_formatada;
      print_r(json_encode($arrayRetorno));
       exit;
    }
    $insert_nfe = \MySql::conectar()->prepare("INSERT INTO `tb_nfe` (`id`, `data`, `data_venda`, `numero_nfe`) VALUES (NULL, ?, ?, ?);");
    $insert_nfe->execute(array($data_emissao,$data_ultima_venda['data'],$n_nfe));
    $insert_nfe = $insert_nfe->fetchAll();



    $nome_empresa = $caixa['caixa'];
    $IE = $caixa['IE']."";
    $cUF =  $caixa['cUF']."";
    $CNPJ = $caixa['CNPJ'].""; //transformar em str

    $nfe = new Make();
    $std = new \stdClass();
    // '2023-12-28-09-20-30'
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
    $std->versao = '4.00';
    $std->Id =null;
    $std->pk_nItem = '';
    $nfe->taginfNFe($std);

    $std = new \stdClass();
    $std->cUF = 31; //coloque um código real e válido
    $std->cNF = '97626321';
    $std->natOp = 'VENDA';
    $std->mod = 55;
    $std->serie = 1;
    $std->nNF = $n_nfe;
    $std->dhEmi = date('Y-m-d\TH:i:sP');
    $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
    $std->tpNF = 1;
    $std->idDest = 1;
    $std->cMunFG = 3133808; //Código de município precisa ser válido
    $std->tpImp = 1;
    $std->tpEmis = 1;
    $std->cDV = 2;
    $std->tpAmb = 1; // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
    $std->finNFe = 1;
    $std->indFinal = 1;
    $std->indPres = 0;
    $std->procEmi = '0';
    $std->verProc = 1;
    $nfe->tagide($std);

    $std = new \stdClass();
    $std->xNome = $nome_empresa;
    $std->IE = $IE;
    $std->CRT = 3;
    $std->CNPJ = $CNPJ;
    $nfe->tagemit($std);
    $infoEnd = json_decode(file_get_contents("https://brasilapi.com.br/api/cep/v1/".$caixa['CEP']),true);
   
    $std = new \stdClass();
    $std->xLgr =strtoupper($infoEnd["street"]);
    $std->nro = $caixa['numero'];
    $std->xBairro = strtoupper($infoEnd["neighborhood"]);
    $std->cMun = $caixa["cMunicipio"]; //Código de município precisa ser válido e igual o  cMunFG
    $std->xMun =$infoEnd["city"];
    $std->UF = $infoEnd["state"];
    $std->CEP = $caixa['CEP']."";
    $std->cPais = '1058';
    $std->xPais = 'BRASIL';
    $nfe->tagenderEmit($std);

    $cpf_cliente = str_replace(".","",str_replace("-","",$_POST["cpf_nfe"]));
    $std = new \stdClass();
    $std->xNome = $_POST["nome_cliente"];
    $std->indIEDest = 9;
    $std->CPF = $_POST["cpf_nfe"];

    $nfe->tagdest($std);

    $std = new \stdClass();
    $std->xLgr =strtoupper($infoEnd["street"]);
    $std->nro = $caixa['numero'];
    $std->xBairro = strtoupper($infoEnd["neighborhood"]);
    $std->cMun = $caixa["cMunicipio"]; //Código de município precisa ser válido e igual o  cMunFG
    $std->xMun =$infoEnd["city"];
    $std->UF = $infoEnd["state"];
    $std->CEP = $caixa['CEP']."";
    $std->cPais = '1058';
    $std->xPais = 'BRASIL';
    $nfe->tagenderDest($std);

    $valor_total_produtos = 0;
    $valor_total_icms = 0;

    foreach ($vendas_com_ultima_data as $key => $value) {
        $valor_produto = $value['valor'];
        $quantidade = $value['quantidade_produto'];
        $valor_total_produtos = $valor_produto + $valor_total_produtos;
        // print_r($vendas_com_ultima_data);

        $arrayRetorno['retorno']['quantidade'] = $quantidade;

        if ($value['por_peso'] == 1) {
            $UN = 'KG';
        } else {
            $UN = 'UNID';
        }
        $pICMS = $value['icms'];
        $IPI = 0;
        $PIS = 0;
        $COFINS = 0;
        $valor_total_tributos = ($pICMS / 100 * $valor_produto) + ($IPI / 100 * $valor_produto) + ($PIS / 100 * $valor_produto) + ($COFINS / 100 * $valor_produto);
        $item = $key + 1;

        $valorICMS = number_format($valor_produto * ($pICMS / 100), 2, '.', '');
        $valor_total_icms = $valorICMS + $valor_total_icms;
        // $arrayRetorno['retorno'][$value['nome']] = number_format($valor_total_icms, 2, '.', '');

        $std = new \stdClass();
        $std->item = $item;
        $std->cEAN = 'SEM GTIN';
        $std->cEANTrib = 'SEM GTIN';
        $std->cProd = $value['codigo'];
        $std->xProd = $value['nome'];
        $std->NCM = str_replace('.', '', $value['ncm']);
        $std->CFOP = '5102';
        $std->uCom = $UN;
        $std->qCom = $quantidade;
        $std->vUnCom = str_replace(',', '.', $value["preco"]);
        $std->vProd =   $valor_produto;
        $std->uTrib = $UN;
        $std->qTrib = $quantidade;
        $std->vUnTrib = str_replace(',', '.', $value["preco"]);
        $std->indTot = 1;
        $nfe->tagprod($std);
        // echo $quantidade;
        // echo $valor_produto;
        // echo  str_replace(',', '.', $value["preco"]);

        $std = new \stdClass();
        $std->item = $item;
        $std->vTotTrib = 10.99;
        $nfe->tagimposto($std);

        $std = new \stdClass();
        $std->item = $item;
        $std->orig = 0;
        $std->CST = '00'; //codigo do icms (CST_ICMS na tabela) funciona com 00
        $std->modBC = 1;
        $std->vBC = $valor_produto; // valor do produto
        $std->pICMS = $pICMS; //porcentagem do icms (ICMS na tabela)
        $std->vICMS = $valorICMS; // valor icms porcentagem X valor do produto
        $nfe->tagICMS($std);


        $std = new \stdClass();
        $std->item = $item;
        $std->cEnq = '999';
        $std->CST = '50';
        $std->vIPI =  ($IPI / 100) * $valor_produto;
        $std->vBC = $valor_produto;
        $std->pIPI = $IPI;
        $nfe->tagIPI($std);

        $std = new \stdClass();
        $std->item = $item;
        $std->CST = '99'; //codigo do PIS (CST_PIS_COFINS na tabela)
        $std->vBC = $valor_produto;
        $std->pPIS = 0; //PIS DA MIX
        $std->vPIS = ($PIS / 100) * $valor_produto; // valor PIS porcentagem X valor do produto
        $nfe->tagPIS($std);

        $std = new \stdClass();
        $std->item = $item;
        $std->CST = '99';
        $std->vBC =  $valor_produto;
        $std->pCOFINS = $COFINS;
        $std->vCOFINS = ($COFINS / 100) * $valor_produto;
        $std->qBCProd = 0;
        $std->vAliqProd = 0;
        $nfe->tagCOFINS($std);

        $std = new \stdClass();
        $std->item = $item;
        $std->vCOFINS =  ($PIS / 100) * $valor_produto;
        $std->vBC = $valor_produto;
        $std->pCOFINS = $COFINS;

        $nfe->tagCOFINSST($std);
    }


    $std = new \stdClass();
    $std->vBC = $valor_total_produtos;
    $std->vICMS = $valor_total_icms;
    $std->vICMSDeson = 0.00;
    $std->vBCST = 0.00;
    $std->vST = 0.00;
    $std->vProd = $valor_total_produtos;
    $std->vFrete = 0.00;
    $std->vSeg = 0.00;
    $std->vDesc = 0.00;
    $std->vII = 0.00;
    $std->vIPI = 0.00;
    $std->vPIS = 0.00;
    $std->vCOFINS = 0.00;
    $std->vOutro = 0.00;
    $std->vNF = $valor_total_produtos;
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
    $std->vPag = $valor_total_produtos;
    $nfe->tagdetPag($std);

    $xml = $nfe->getXML();
    $caminhoArquivo = '../../NFE/xml/Nfe_' . $data_emissao . '.xml';
    // Tenta salvar o XML no arquivo

    // if (file_put_contents($caminhoArquivo, $xml) !== false) {
    //     $arrayRetorno['retorno']['XML'] = 'XML da NFe salvo com sucesso!';
    // } else {
    //     $arrayRetorno['retorno']['errorXML'] =  'Erro ao salvar o XML da NFe.';
    // }
    // print_r($xml);
    $config  = [
        "atualizacao" => date('Y-m-d h:i:s'),
        "tpAmb" => 2,
        "razaosocial" => $nome_empresa,
        "cnpj" => $CNPJ, // PRECISA SER VÁLIDO
        "ie" => $IE, // PRECISA SER VÁLIDO
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
        $danfe->logoParameters($logo, 'C', false);
        //Gera o PDF
        $pdf = $danfe->render($logo);
        header('Content-Type: application/pdf');
        criarArquivoNFe($data_emissao, 'pdf', $pdf);

        // if (file_put_contents('../../NFE/pdf/Nfe_' . $data_emissao . '.pdf', $pdf) !== false) {
        //     $arrayRetorno['retorno']['PDF'] =  'Sucesso Pdf';
        // } else {
        //     $arrayRetorno['retorno']['errorPDF'] = 'Erro ao salvar o XML da NFe.';
        // }
    } catch (InvalidArgumentException $e) {
        $arrayRetorno['retorno']['error'] = "Ocorreu um erro durante o processamento :" . $e->getMessage();
    }
  

    $configJson = json_encode($config);
    // $certificadoDigital = file_get_contents('certificado.pfx');}
    $path = "../../certificados/".strtoupper($infoEnd["street"])."/";
    $diretorio =scandir($path);
    $arquivo = $diretorio[2];

    $caminhoCertificado = $path.$arquivo;
    // echo $caminhoCertificado;
    $certificadoDigital = file_get_contents($caminhoCertificado);
    $tools = new NFePHP\NFe\Tools($configJson, NFePHP\Common\Certificate::readPfx($certificadoDigital, '123456'));

    try {
        $xmlAssinado = $tools->signNFe($xml); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
        criarArquivoNFe($data_emissao, 'xml', $xmlAssinado );
    } catch (\Exception $e) {
        //aqui você trata possíveis exceptions da assinatura
   
        exit($e->getMessage());
    }
    try {
        $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
        $resp = $tools->sefazEnviaLote([$xmlAssinado], $idLote);
    
        $st = new NFePHP\NFe\Common\Standardize();
        $std = $st->toStd($resp);
        if ($std->cStat != 103) {
            //erro registrar e voltar
            exit("[$std->cStat] $std->xMotivo");
        }
        $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota
    } catch (\Exception $e) {
        //aqui você trata possiveis exceptions do envio
        exit($e->getMessage());
    }
    try {
        $protocolo = $tools->sefazConsultaRecibo($recibo);
        // print_r($protocolo);

    } catch (\Exception $e) {
        //aqui você trata possíveis exceptions da consulta
        exit($e->getMessage());
    };

$request = $xmlAssinado;
$response = $protocolo;
    try {
        $xml = Complements::toAuthorize($request, $response);
        // header('Content-type: text/xml; charset=UTF-8');

    } catch (\Exception $e) {
        echo "Erro: " . $e->getMessage();
    }
    print_r(json_encode($arrayRetorno));

}

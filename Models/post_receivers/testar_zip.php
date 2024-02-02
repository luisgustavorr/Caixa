<?php 
$caminho = "C:\\Users\\Public\\Documents\\NotasFiscais\\xml\\2024\\01\\25\\2024-01-25-16-25-45.xml";
$xmlstring = file_get_contents($caminho);
$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
$produtos = $array["infNFe"]["det"];
foreach ($produtos as $key => $value) {
    $produto = $value["prod"];
    $nomeProduto = $produto["xProd"];
    $valorUnidade = $produto["vUnCom"];
    $quantidade = $produto["qCom"];
    $codigoProduto = $produto["cProd"];
    $codigoProduto = $produto["cProd"];
    $cEANTrib = $produto["cEANTrib"];
    $ncm = $produto["NCM"];
    $CFOP = $produto["CFOP"];
    $uCom = $produto["uCom"];

}
?>
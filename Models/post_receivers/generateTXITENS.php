<?php 
include('../../MySql.php');
// DDTTZCCCCCCPPPPPPVVVDDEEEEE

// ONDE: DD = 2 CARACTERES DE No. DO DEPARTAMENTO
// TT = 2 CARACTERES DE TIPO DE ETIQUETA
// Z = 1 CARACTER DE TIPO DE PRECO (kg OU UNID)
// CCCCCC = 6 CARACTERES DE CODIGO
// PPPPPP = 6 CARACTERES DE PRECO UNITARIO
// VVV = 3 CARACTERES DE DIAS DE VALIDADE
// D1 = 50 CARACTERES DE DESCRITIVO (LINHA 1)
// D2 = 50 CARACTERES DE DESCRITIVO (LINHA 2)
// EEEEE = 250 CARACTERES DE INF. EXTRA (PARA RECEITA DE 50 EM 50 CARACTERES)
// JSON_EXTRACT(tb_produtos.json_precos, \'$."'.$_COOKIE["caixa"].'"\')
    // Consulta SQL para obter os dados
    $caminho_arquivo = 'C:\\Users\\Public\\Documents\\TXITENSQendra\\txitens.txt';
    if(file_exists($caminho_arquivo)){
        unlink($caminho_arquivo);
    };
    $consulta = \MySql::conectar()->prepare('SELECT 
    id,
    preco,
    "01" as cod_grp_financeiro,
    "03" as etiqueta,
    CASE 
        WHEN por_peso = 1 THEN "0" 
        ELSE "0" 
    END AS por_peso_formatado,
    LPAD(codigo_id, 6, "0") AS codigo_formatado,
    LPAD(LPAD(REPLACE(FORMAT(REPLACE(REPLACE(JSON_EXTRACT(tb_produtos.json_precos, \'$."'.$_COOKIE["caixa"].'"\'), \'"\', \'\'), ",", ""), 2), ".", ""), 6, "0"), 6, "0") AS preco_formatado, 
    LPAD(validade, 3, "0") as validade,
    nome as Nome 
FROM 
    tb_produtos 
ORDER BY 
    id DESC;
');
    $consulta->execute();
    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
    // Nome do arquivo CSV
    foreach ($resultado as $row) {
        $linha =  $row['cod_grp_financeiro'].$row['etiqueta'].$row['por_peso_formatado'].$row['codigo_formatado'].$row['preco_formatado'].$row['validade'].$row['Nome']." KG";
      
        // echo $linha;
        file_put_contents($caminho_arquivo, $linha . PHP_EOL, FILE_APPEND);
    }
    echo "Sucesso";
?>
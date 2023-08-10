<?php
$directory = "C:\\NewerXampp\\htdocs\\MixSalgados\\Caixa"; // Caminho completo para o diretório

// Comando para executar o git pull
$command = "cd $directory && git pull";

// Executa o comando git pull
$output = array();
$returnCode = 0;
exec($command, $output, $returnCode);

// Verifica o código de retorno para determinar se o comando foi bem-sucedido
if ($returnCode === 0) {
    echo "Git pull realizado com sucesso no diretório $directory";
    echo "<pre>" . implode("\n", $output) . "</pre>"; // Exibe a saída do comando Git
} else {
    echo "Erro ao tentar realizar o git pull.";
    print_r($output); // Exibe qualquer saída ou erro retornado pelo comando Git
}
?>

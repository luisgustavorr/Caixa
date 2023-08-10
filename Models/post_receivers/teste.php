<?php 
$directory = "C:\\NewerXampp\\htdocs\\MixSalgados\\Caixa"; // Caminho completo para o diretório
$command = "git pull"; // Comando Git para executar

// Altera o diretório atual para o diretório desejado
chdir($directory);

// Executa o comando git pull
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
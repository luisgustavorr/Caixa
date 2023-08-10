<?php
$directory = "C:\\NewerXampp\\htdocs\\MixSalgados\\Caixa"; // Caminho completo para o diretório

// Comandos para limpar o cache do Git, adicionar todas as mudanças e fazer commit vazio
$commands = array(
    "cd $directory",
    "git rm -r --cached .",
    "git add .",
    "git commit -m 'Limpar cache do Git'",
    "git pull"
);

// Executa os comandos em sequência
$output = array();
$returnCode = 0;
foreach ($commands as $command) {
    exec($command, $output, $returnCode);
    if ($returnCode !== 0) {
        echo "Erro ao executar o comando: $command<br>";
        print_r($output);
        break;
    }
    echo "Comando executado: $command<br>";
    echo "<pre>" . implode("\n", $output) . "</pre>";
    $output = array(); // Limpa a saída para o próximo comando
}
?>

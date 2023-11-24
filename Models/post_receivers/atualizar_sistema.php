<?php

// Executa o comando 'git fetch --all' para buscar atualizações do repositório remoto
shell_exec('git fetch --all');

// Obtém a hash do commit local
$localCommitHash = trim(shell_exec('git rev-parse HEAD'));

// Obtém a hash do commit remoto
$remoteCommitHash = trim(shell_exec('git rev-parse origin/main'));

// Compara as hashes para verificar se o código está atualizado
if ($localCommitHash === $remoteCommitHash) {
    echo "O código está atualizado.\n";
} else {
    echo "O código precisa ser atualizado.\n";

    // Executa 'git reset --hard origin/main' para atualizar o código local
    shell_exec('git reset --hard origin/main');

    echo "O código foi atualizado para a versão mais recente.\n";
}
?>


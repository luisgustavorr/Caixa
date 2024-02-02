<?php
$porta_serial = fopen("COM1", "r"); // Abre a porta serial COM1 para leitura

if ($porta_serial) {
    $linha = fgets($porta_serial); // Lê uma linha da porta serial

    if ($linha !== false) {
        echo $linha; // Exibe a linha lida
    } else {
        echo "Nenhuma linha foi lida.";
    }

    fclose($porta_serial); // Fecha a porta serial
} else {
    echo "Falha ao abrir a porta serial COM1.";
}
?>

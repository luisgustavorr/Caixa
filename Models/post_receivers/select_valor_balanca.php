
<?php
$port = 'COM5'; // A porta serial à qual a balança está conectada
$baud = 9600;   // Taxa de baud (velocidade de comunicação) da balança

$serial = fopen($port, 'r+');

if ($serial) {
    // Configurar a porta serial
    stream_set_timeout($serial, 5); // Tempo limite para leitura

    // Ler dados da balança
    $data = fread($serial, 1024); // Leitura de até 1024 bytes

    // Fechar a porta serial
    fclose($serial);

    // Processar os dados recebidos da balança (os dados podem ser formatados de acordo com o protocolo da balança)
    
    // Suponha que os dados recebidos sejam simplesmente o peso em quilogramas
    $weightInKg = floatval($data);

    // Exibir o peso
    echo $weightInKg ;
} else {
    echo "Não foi possível abrir a porta serial.";
}
?>
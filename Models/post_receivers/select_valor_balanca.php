<?php 
require_once 'PhpSerial.php';

// Configurações da porta serial
$port = '/dev/ttyS0'; // Ou 'COM1' no Windows
$baud_rate = 9600;
$data_bits = 8;
$stop_bits = 1;
$parity = 'none';

// Criar uma instância da classe phpSerial
$serial = new phpSerial;

// Configurar as propriedades da porta serial
$serial->deviceSet($port);
$serial->confBaudRate($baud_rate);
$serial->confCharacterLength($data_bits);
$serial->confStopBits($stop_bits);
$serial->confParity($parity);

// Abrir a porta serial
$serial->deviceOpen();

// Comando para solicitar o peso da balança (exemplo)
$command = "P"; // Consulte o manual para o comando correto

// Enviar o comando para a balança
$serial->sendMessage($command);

// Aguardar um curto período de tempo para receber a resposta
usleep(100000); // Aguarde 0,1 segundo (ajuste conforme necessário)

// Ler a resposta da balança
$response = $serial->readPort();

// Fechar a porta serial
$serial->deviceClose();

// Processar a resposta recebida (o formato depende do protocolo da balança)
echo "Resposta da balança: " . $response;
?>
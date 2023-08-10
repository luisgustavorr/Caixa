<?php 
/* Change to the correct path if you copy this example! */
require __DIR__ . '/../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;


try {

    $connector = new WindowsPrintConnector(dest:"TM-T20X");

    $printer = new Printer($connector);
$printer->text("SANGRIA DE CAIXA\n");
$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
$printer->text("Data: " . date("d/m/Y H:i:s") . "\n"); // Adicione a data e hora da sangria

// Escreve os detalhes da sangria
$valorSangria = 100.00; // Valor da sangria (substitua pelo valor correto)
$motivoSangria = "Retirada para troco"; // Motivo da sangria (substitua pelo motivo correto)

$printer->text("Valor Sangria: R$" . number_format($valorSangria, 2, ',', '.') . "\n");
$printer->text("Motivo: " . $motivoSangria . "\n");

// Escreve o rodapé da mensagem
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
$printer->text("Assinatura _______________________\n");

// Finaliza a impressão e fecha a conexão
$printer->cut();
$printer->close();
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}

?>

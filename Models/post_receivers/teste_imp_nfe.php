<?php 
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\ImagickEscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
$pdf = './teste.pdf';
$connector = new WindowsPrintConnector(dest : 'TM-T20X');
            
  $printer = new Printer($connector);
      
      $pages = ImagickEscposImage::loadPdf($pdf);
      foreach ($pages as $page) {
          $printer->graphics($page);
      }
      $printer->cut();
      /*
   * loadPdf() throws exceptions if files or not found, or you don't have the
   * imagick extension to read PDF's
   */

      $printer->close();
  
?>
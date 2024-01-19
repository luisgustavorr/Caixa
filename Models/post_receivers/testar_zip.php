<?php
function toggleZipExtension($enable = true) {
    $phpIniPath = php_ini_loaded_file();
    echo $phpIniPath;
    if ($phpIniPath) {
        $contents = file_get_contents($phpIniPath);

        if ($enable) {
            // Descomentar a extensão zip
            $contents = str_replace(";extension=zip", "extension=zip", $contents);
        } else {
            // Comentar a extensão zip
            $contents = str_replace("extension=zip", ";extension=zip", $contents);
        }

        // Salvar o conteúdo modificado em um novo arquivo
        file_put_contents($phpIniPath, $contents);
        echo 'php.ini modificado com sucesso!';
    } else {
        echo 'php.ini não encontrado.';
    }
}

// Usar a função para descomentar a extensão zip
toggleZipExtension(true); 
// function Compress($source_path)
// {
//     // Normaliza o caminho do diretório a ser compactado
//     $source_path = realpath($source_path);

//     // Caminho com nome completo do arquivo compactado
//     // Nesse exemplo, será criado no mesmo diretório de onde está executando o script
//     $zip_file = __DIR__.'/'.basename($source_path).'.zip';

//     // Inicializa o objeto ZipArchive
//     $zip = new ZipArchive();
//     $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

//     // Iterador de diretório recursivo
//     $files = new RecursiveIteratorIterator(
//         new RecursiveDirectoryIterator($source_path),
//         RecursiveIteratorIterator::LEAVES_ONLY
//     );

//     foreach ($files as $name => $file) {
//         // Pula os diretórios. O motivo é que serão inclusos automaticamente
//         if (!$file->isDir()) {
//             // Obtém o caminho normalizado da iteração corrente
//             $file_path = $file->getRealPath();

//             // Obtém o caminho relativo do mesmo.
//             $relative_path = substr($file_path, strlen($source_path) + 1);

//             // Adiciona-o ao objeto para compressão
//             $zip->addFile($file_path, $relative_path);
//         }
//     }

//     // Fecha o objeto. Necessário para gerar o arquivo zip final.
//     $zip->close();

//     // Retorna o caminho completo do arquivo gerado
//     return $zip_file;
// }

// // O diretório a ser compactado
// $source_path = 'C:\Users\Public\Documents\NotasFiscais\xml\2024';
// echo Compress($source_path);
<?php
include('MySql.php');
define('INCLUDE_PATH', 'http://localhost/MixSalgados/Caixa/');
define('INCLUDE_PATH_FULL', INCLUDE_PATH.'/View/pages/');
define('CARGOS_DB_PASSWORD','///senha///');
spl_autoload_register(
    function($class){
        $path = str_replace("\\",DIRECTORY_SEPARATOR,$class);
        $path_to_file = $path.".php";
        if($path == 'Application'){
            require_once('Application.php');
        }elseif( $path == 'MySql.php'){
            require_once('MySql.php');

        }

        elseif(file_exists($path_to_file)){
            require_once($path_to_file);
    }else{
      include('View/pages/templates/header.php');
      include('View/pages/templates/404page.php');
 

     
    }
    }
  );
use Dotenv\Dotenv;


date_default_timezone_set('America/Sao_Paulo');


define('BASE_DIR_PAINEL', __DIR__);
?>

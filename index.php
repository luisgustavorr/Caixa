<?php 
/*          MVC
        M= Model
        V = View
        C= Controller
        /contato
        Contato Controller => Controlador geral. Podemos executar o model e a view
        Contato View => Responsável por renderizar a página
        Contato Model => Onde tem as funções



*/

                                            require_once './vendor/autoload.php';
include('config.php');
$app = new Application();
$app->executar();
?>
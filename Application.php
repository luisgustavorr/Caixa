<?php

class Application 
{
    public function executar()
    {
        
        $url = isset($_GET['url']) ? $_GET['url'] : 'Home';
        $url = ucfirst($url);
        $className = 'Controllers\\' . $url . 'Controller';
        $viewName = strtolower($url);
        $controllerFile = 'Controllers/' . $url . 'Controller.php';
        $viewFile = 'View/pages/' . $viewName . '.php';
        // Verifica se o arquivo do controlador e a view existem
        if (file_exists(str_replace('DashBoard/','',$controllerFile)) ) {
            include_once( str_replace('DashBoard/','',$controllerFile));
            $className = str_replace('DashBoard/','',$className);
            $controller = new $className();
            $controller->executar();
        } else {
            // Rota não encontrada
            echo "Página não encontrada!";
        }
    }
}
/*  for ($i=0; $i < 30; $i++) { 
            echo"(NULL, 'nome', 'descricao', '100.00,00', '0', 'luis', '0', '1'),";
          }*/
?>
<?php

class Application 
{
    public function executar()
    {
        
        $url = '';
        if(!isset($_GET['url'])){
            $url = 'Home';
        }else{  
            $url = $_GET['url'];
            $url = $url == 'DashBoard/' ? 'DashBoard/Login' : $_GET['url'];

        }
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

?>
<?php 
namespace Controllers;
class LoginController
{
    
    public function __construct()
    {

        $this->view = new \View\MainView('login');
    }
    public function executar()
    {
        isset($_COOKIE['login']) ?  $this->view = new \View\MainView('painel_controle') :  $this->view = new \View\MainView('login');
        
      
       
        
            $this->view ->render(array('titulo'=>'Painel de Controle'));
    }
}

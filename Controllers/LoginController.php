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
        if( \Painel::logado() ==true){ echo"<script>location.href='RelatorioABC'</script>";}else{$this->view = new \View\MainView('login');}
        
      
       
        
            $this->view ->render(array('titulo'=>'Painel de Controle'));
    }
}

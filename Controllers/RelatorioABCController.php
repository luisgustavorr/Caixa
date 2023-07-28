<?php 
namespace Controllers;
class RelatorioABCController
{
    
    public function __construct()
    {

        $this->view = new \View\MainView('painel_controle');
    }
    public function executar()
    {
        if( \Painel::logado() ==false){$this->view = new \View\MainView('login');}else{$this->view = new \View\MainView('painel_controle');}
        
      
       
        
            $this->view ->render(array('titulo'=>'Painel de Controle'));
    }
}

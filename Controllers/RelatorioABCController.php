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
        $this->view = new \View\MainView('painel_controle');
        
      
       
        
            $this->view ->render(array('titulo'=>'Painel de Controle'));
    }
}

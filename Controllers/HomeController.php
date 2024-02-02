<?php 
namespace Controllers;
class HomeController 
{
    
    public function __construct()
    {

        $this->view = new \View\MainView('home');
    }
    public function executar()
    {
     $this->view = new \View\MainView('home');
      
       
        
            $this->view ->render(array('titulo'=>'Home'));
    }
}

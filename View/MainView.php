<?php

namespace View;

class MainView
{
    private $fileName;
    private $header;

    public function __construct($fileName, $header = 'header', )
    {
        $this->fileName = $fileName;
        $this->header = $header;

    }
    public function render()
    {
        @session_start();





            include('pages/templates/' . $this->header . '.php');
            include('pages/' . $this->fileName . '.php');
        
    }
}

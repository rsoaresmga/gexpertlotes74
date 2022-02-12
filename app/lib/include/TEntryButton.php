<?php

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TEntry;

class TEntryButton extends TElement {           
      public $button;
      public $edit;
      
      function __construct($class, $param) {
            
            parent::__construct('div');

           
            $params = func_get_args();

            unset($params[0]);

            print_r($params)[1];

            $this->edit = new $class($params);
            $this->placeholder = $placeholder;

            $this->button = new TButton('btn_'.$name);
            $this->button->setLabel($label);
            $this->button->{'style'} = 'border-radius: 0; top:0; bottom:0; padding: .375rem .75rem';
            

            parent::add($this->edit);
            parent::add($this->button);            
      }

      function setButtonAction($action, $label)
      {
            $this->button->setAction($action, $label);
      }
}

?>
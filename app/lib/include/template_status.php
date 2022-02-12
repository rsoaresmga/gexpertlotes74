
<?php

/*
    Autor: Rodrigo Soares
    Data : 19/01/2021
    Juntamente com o AdiantiTemplateParser.php e funcoes javascript no layout.xml 
    esse arquivo registra os estados de click do usuario no template e os 
    salva em variavel de sessao.  
*/
try {
       // require_once 'init.php';
      
       new TSession;
      
      
      // Registra o estado do menu recolhido ou expandido    
      TSession::setValue('menu_collapsed', 'aaaa');
      
      //session_start();
     //$_SESSION[APPLICATION_NAME]['AAA'] = 'AAA';
      
      echo json_encode($_POST);
      
     // throw new Exception('Erroi');
      
      
 } catch (Exception $e) 
 {
     echo json_encode($e->getMessage());
 }
      
?>

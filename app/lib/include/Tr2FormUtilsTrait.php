<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TSlider;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\AdiantiFormInterface;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Form\TCheckGroup;
use Adianti\Widget\Form\TMultiEntry;
use Adianti\Widget\Util\TActionLink;
use Adianti\Widget\Wrapper\TDBRadioGroup;
use Adianti\Widget\Wrapper\TDBCheckGroup;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Registry\TSession;
use Adianti\Wrapper\BootstrapFormBuilder;

trait Tr2FormUtilsTrait
{
    
    function setDefaultPageAction($param = null)
    {
        $this->form->setTabAction(new TAction([$this, 'registerCurrentPage'], $param));
    }
    
    function registerCurrentPage($param)
    {
        $page_index = null;
        
        if(isset($param['page']) and ($param['page']>-1))
        {
            $page_index = $param['page'];
        }
        else
        if(isset($param['current_page']) and ($param['current_page']>-1))
        {
            $page_index = $param['current_page'];
        } 
        if (!is_null($page_index))
        {
            TSession::setValue(__CLASS__."_current_page", $page_index);    
        } 

        return $page_index;
    }
    
    function getLastCurrentPage()
    {
        $page_index = TSession::getValue(__CLASS__."_current_page");
        
        if (is_null($page_index))
        {
            $page_index = -1; 
        }    
        
        return $page_index;
    }
    
    function setLastCurrentPage($param = null)
    {
        $page_index = $this->getLastCurrentPage();

        if (isset($param['page']) and ($param['page']>-1))
        {
            $page_index = $param['page'];
             
            $this->form->setCurrentPage($page_index);
            
            return $page_index;       
        }
        
        if ($page_index>=0)
        {
            $this->form->setCurrentPage($page_index);  
        } 
        
        return $page_index;               
    }
    
    public function asBooleanBR($value)
    {
       if(trim($value)=='S')
        {
            return 'Sim';               
        }
        return 'Não'; 
    }
    
    public function asDouble($value)
        {
            $double = (double) str_replace(['.',','],['','.'], $value);
                        
            if(isset($double))
            {
                return $double;
            } else {
                       return (double) 0;
                   }
        }
    
    public function asDoubleBR($value)
    {
        if(is_numeric($value))
        {
            $number = number_format($value, 5, ',', '.');

            return $number;
        }
        
        return $value;    
    }
    
    public function asCurBR($value)
    {
        if(is_numeric($value))
        {
            $number = number_format($value, 2, ',', '.');
            
            return 'R$ '.$number;
        }
        
        return $value;      
    } 
    
    public function asInt($value)
    {
        return round(str_replace([',','.'],['',''],$value)) ;
    }
    
    public function asDate($value)
    {
        return TDate::date2br($value);
    }
    
    public function asDateTime($value)
    {
        return TDateTime::convertToMask($value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii:ss'); 
    }
    
    public function asSQLDate($value)
    {
        return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
    }
    
    public function strToHex($string)
    {
        $hex='';
        
        for ($i=0; $i < strlen($string); $i++)
        {
            $hex .= dechex(ord($string[$i]));
        }
        
        return $hex;
    }
    
    public function hexToStr($hex)
    {
        $string='';
       
        for ($i=0; $i < strlen($hex)-1; $i+=2)
        {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        
        return $string;
    }
        
    public function asShortDate($date)
    {
        if ($date)
        {
            // get the date parts
            $year = substr($date,2,2);
            $mon  = substr($date,5,2);
            $day  = substr($date,8,2);
            return "{$day}/{$mon}/{$year}";
        }
    }
    
    public function formatNumber($value, $decimals)
    {
        return number_format($value, $decimals, ',', '.');
    } 
    
    public function formatDate($value, $fromMask = 'yyyy-mm-dd', $toMask = 'dd/mm/yyyy')
    {
        return TDate::convertToMask($value, $fromMask, $toMask);
    }
    
    public function asDateSQL($value)
    { 
        return TDate::date2us($value); 
    }
    
    public function asBooleanSQL($value)
    { 
        return substr($value,1); 
    }
    
    public function asCurrencySQL($value)
    {                       
       if ($value)
       {
           if((strpos($value, ',')>0) || (strpos($value, '.')>0)) 
           {
               $value = rtrim(rtrim($value,'0'),'.');    
           }                                                                 

           $value = rtrim(str_replace(',','.', str_replace('.', '', $value)), '.');
           
           return $value;                            
       }    
        
       return 0;    
    }
    
    public function sumCurrency($values)
    {
        return 'R$ '.number_format(array_sum($values), 2, ',', '.');    
    } 
    
    public function sumDouble($values)
    {
        return number_format(array_sum($values), 5, ',', '.');    
    }
    
    public function arrayCount($array)
    {
        return count($array);
    }
    
    public function asTipoPessoa($value)
    {
        if($value=='J')
        {
            return 'Jurídica';
        }
        
        return 'Física';
    }
    
             
}

?>

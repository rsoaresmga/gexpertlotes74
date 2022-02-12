<?php

    class TConversion 
    {
        public static function asDouble($value)
        {
            $double = (double) str_replace(['.',','],['','.'], $value);
                        
            if(isset($double))
            {
                return $double;
            } else {
                       return (double) 0;
                   }
        }
        
        public static function asDoubleBR($value, $decimals)
        {
            $number = number_format($value, $decimals);

            return str_replace([',', '.'],['', ','], $number);
        }
        
        public static function asInt($value)
        {
            return round(str_replace([',','.'],['',''],$value)) ;
        }
        
        public static function asDate($value)
        {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        }
        
        public static function asDateTime($value)
        {
            return TDateTime::convertToMask($value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii:ss'); 
        }
        
        public static function asSQLDate($value)
        {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        }
        
        public static function strToHex($string)
        {
            $hex='';
            
            for ($i=0; $i < strlen($string); $i++)
            {
                $hex .= dechex(ord($string[$i]));
            }
            
            return $hex;
        }
        
        function hexToStr($hex)
        {
            $string='';
            for ($i=0; $i < strlen($hex)-1; $i+=2)
            {
                $string .= chr(hexdec($hex[$i].$hex[$i+1]));
            }
            return $string;
        }
        
         /**
     * Shortcut to convert a date to format dd/mm/yy
     * @param $date = date in format yyyy-mm-dd
     */
    public static function asShortDate($date)
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
    
    public function asBoolean($value)
    {
        if(trim($value)=='S')
        {
            return 'Sim';               
        }
        return 'Não';       
    }

  }

?>

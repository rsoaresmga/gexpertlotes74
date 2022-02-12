<?php
    class TUtil
    {
        
        public static function coalesce($value, $default)
        {
            return !empty($value)?$value:$default;
        }
        
        public static function format($type, $val)
        {
            $value = $val;            
            
            switch ($type)
            {
                case 'date': 
                {
                    $value = TConversion::asDate($value);
                  break;   
                } 
                case 'datetime': 
                {
                    $value = TConversion::asDateTime($value);
                  break;   
                }                             
                case 'double':
                {
                    $value = TConversion::asDoubleBR($value,5);
                  break;
                }
                case 'shortdate':
                {
                    $value = TConversion::asShortDate($value);
                    break;
                }
            } 
            
            return $value;  
        }
        
        public static function arr_to_str(string $delim, array $arr)
        {
            return implode($delim, array_column($arr));
        }
        
         /**
         * @name
         * @caption
         * @size
         * @align
         * @total
         */ 
        public static function str_to_field($name, $caption, $size, $align, $total = null, $type = null)
        {
            return array('name'=>$name, 'caption'=>$caption, 'size'=>$size, 'align'=>$align, 'total'=>$total, 'type'=>$type);   
        }
        
        /**
        * @field
          @footer
          @header
        */
        public static function str_to_group($field, $footer, $header)
        {
           return array('field'=>$field, 'footer'=>$footer, 'header'=>$header); 
        }
             
    }
?>

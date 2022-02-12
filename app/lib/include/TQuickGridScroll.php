<?php

use Adianti\Widget\Datagrid\TDataGrid;

class TQuickGridScroll extends TDataGrid
{
      function __construct() {
            parent::__construct();

            $this->makeScrollable();
            $this->{'style'} .= 'width: calc(100% - 14px) !important;';
      } 

      /**
     * Add a column
     * @param $label  Field Label
     * @param $object Field Object
     * @param $size   Field Size
     */
    public function addQuickColumn($label, $name, $align = 'left', $size = 200, TAction $action = NULL, $param = NULL)
    {
        // creates a new column
        $object = new TDataGridColumn($name, $label, $align, $size);
        
        if ($action instanceof TAction)
        {
            // create ordering
            $action->setParameter($param[0], $param[1]);
            $object->setAction($action);
        }
        // add the column to the datagrid
        parent::addColumn($object);
        return $object;
    }
    
    /**
     * Add action to the datagrid
     * @param $label  Action Label
     * @param $action TAction Object
     * @param $icon   Action Icon
     */
    public function addQuickAction($label, TDataGridAction $action, $field, $icon = NULL)
    {
        $action->setLabel($label);
        if ($icon)
        {
            $action->setImage($icon);
        }
        
        if (is_array($field))
        {
            $action->setFields($field);
        }
        else
        {
            $action->setField($field);
        }
        
        // add the datagrid action
        parent::addAction($action);
        
        return $action;
    }

    function show()
    {
      parent::show();
      
      TScript::create("
        $('#{$this->id}').addClass('table-bordered');
        
        $('#{$this->id} thead tr:gt(0)').each( (index, item) =>  { $(item).attr('style', $(`#{$this->id} tr:eq(0)`).attr('style')) });
        $('#{$this->id} thead th').each( (index, item) =>  { $( '#{$this->id} tfoot td' ).eq( index ).attr('width', $(item).attr('width'))  });
        
        $( '#{$this->id} thead tr:gt(0) td').each( ( index, item ) => { $(item).attr('style', $('#{$this->id} th').eq(index).attr('style')); $(item).attr('width', $('#{$this->id} th').eq(index).attr('width')) }); 
        $( '#{$this->id} tbody td').each( ( index, item ) => { $(item).attr('width', $('#{$this->id} th').eq(index).attr('width')) }); 
        
        $( '#{$this->id} tbody tr' ).css('display', 'inline-table').css('width', 'calc(100% - 20px)');
      ");
    }
}

?>
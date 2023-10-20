<?php


class InternetCode_Translator_Block_Adminhtml_Missing extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'ic_translator';
        $this->_controller = 'adminhtml_missing';
        $this->_headerText = $this->__('Missing Translations');
        parent::__construct();
        $this->removeButton('add');

        $this->_addButton('clear', [
            'label' => 'Clear All',
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/clearAll') . '\')',
            'class' => 'delete',
        ]);
    }
}


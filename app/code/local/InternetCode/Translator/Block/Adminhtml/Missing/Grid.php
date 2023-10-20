<?php


class InternetCode_Translator_Block_Adminhtml_Missing_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        // $this->setDefaultSort('COLUMN_ID');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ic_translator/missing')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('id',
            [
                'header' => 'ID',
                'width' => '50px',
                'index' => 'id'
            ]
        );

        $this->addColumn('module',
            [
                'header' => 'Module',
                'width' => '50px',
                'index' => 'module',
                'type' => 'options',
                'options' => Mage::getModel('ic_translator/missing')->getCollection()->getModulesOptionArray()
            ]
        );


        $this->addColumn('locale',
            [
                'header' => 'Locale',
                'width' => '100px',
                'index' => 'locale',
                'type' => 'options',
                'options' => Mage::getModel('ic_translator/missing')->getCollection()->getLocalesOptionArray(),
            ]
        );
        $this->addColumn('text',
            [
                'header' => 'Original Text',
                'index' => 'text'
            ]
        );
        $this->addColumn('fallback',
            [
                'header' => 'Fallback Text',
                'index' => 'fallback'
            ]
        );

        return parent::_prepareColumns();
    }
}

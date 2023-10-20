<?php


class InternetCode_Translator_Block_Adminhtml_Csv_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        // $this->setDefaultSort('COLUMN_ID');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    public function conditionCallback(
        InternetCode_Translator_Model_Resource_Csvtranslation_Collection $collection,
        Mage_Adminhtml_Block_Widget_Grid_Column                          $column
    )
    {
        $collection->addFilter($column->getFilterIndex() ?: $column->getIndex(), $column->getFilter()->getValue());
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('ic_translator/csvtranslation_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $locales = Mage::app()->getLocale()->getOptionLocales();
        $localesArray = [];
        foreach ($locales as $locale) {
            foreach (Mage::app()->getStores() as $store) {
                if ($locale['value'] == $this->helper('ic_translator')::localeFromStore($store->getId())) {
                    $localesArray[$locale['value']] = $locale['label'];
                }
            }
        }
        $this->addColumn('locale',
            [
                'header' => 'Locale',
                'width' => '100px',
                'index' => 'locale',
                'type' => 'options',
                'options' => $localesArray
            ]
        );

        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        $moduleNameArray = [];
        foreach ($modulesArray as $moduleName => $moduleInfo) {
            $moduleNameArray[$moduleName] = $moduleName;
        }
        ksort($moduleNameArray);
        $this->addColumn('module',
            [
                'header' => 'Module',
                'width' => '100px',
                'index' => 'module',
                'type' => 'options',
                'options' => $moduleNameArray
            ]
        );
        $this->addColumn('text',
            [
                'header' => 'Original Text',
                'index' => 'text'
            ]
        );
        $this->addColumn('translation',
            [
                'header' => 'Translation',
                'index' => 'translation'
            ]
        );

        /** @var Mage_Adminhtml_Block_Widget_Grid_Column $col */
        foreach ($this->_columns as $col) {
            $col->setData('filter_condition_callback', [$this, 'conditionCallback']);
        }

        return parent::_prepareColumns();
    }
}

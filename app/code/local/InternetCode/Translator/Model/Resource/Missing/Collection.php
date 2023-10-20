<?php


class InternetCode_Translator_Model_Resource_Missing_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function getModulesOptionArray()
    {
        $select = clone $this->getSelect();
        $select->reset(Varien_Db_Select::COLUMNS);
        $select->columns(['module', 'module']);
        $select->group('module');

        return $this->getConnection()->fetchPairs($select);
    }

    public function getLocalesOptionArray()
    {
        $select = clone $this->getSelect();
        $select->reset(Varien_Db_Select::COLUMNS);
        $select->columns(['locale', 'locale']);
        $select->group('locale');

        $locales = [];
        foreach ($this->getConnection()->fetchPairs($select) as $localeCode) {
            $data = explode('_', $localeCode);
            $locales[$localeCode] = Zend_Locale::getTranslation($data[0], 'language');
        }

        return $locales;
    }

    protected function _construct()
    {
        $this->_init('ic_translator/missing');
    }

}

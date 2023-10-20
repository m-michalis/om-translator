<?php


class InternetCode_Translator_Model_Resource_Missing extends Mage_Core_Model_Resource_Db_Abstract
{

    public function truncate()
    {
        $this->_getWriteAdapter()->truncateTable($this->getMainTable());
    }

    protected function _construct()
    {
        $this->_init('ic_translator/missing', 'id');
    }

}

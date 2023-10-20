<?php


class InternetCode_Translator_Model_Resource_Csvtranslation_Collection extends Varien_Data_Collection
{

    /**
     * Collection items
     *
     * @var array
     */
    protected $_allItems = [];

    public function addFieldToFilter($field, $value)
    {
        return $this->addFilter($field, $value);
    }

    public function addFilter($field, $value, $type = 'and')
    {
        $filter = new Varien_Object(); // implements ArrayAccess
        $filter->setData('field', $field);

        $value = preg_quote($value, '/'); // Quote regex meta-characters
        $value = str_replace(['%', '_'], ['.*', '.'], $value); // Convert wildcards

        switch ($field) {
            //These are exact match ?
            case "id":
            case "locale":
            case "module":
                $filter->setData('pattern', "/^" . $value . "$/i");
                break;
            //These are LIKE
            case "text":
            case "translation":
            default:

                $filter->setData('pattern', "/" . $value . "/i"); // Assume case-insensitive matching

        }

        $this->_filters[] = $filter;
        $this->_isFiltersRendered = false;
        return $this;
    }

    /**
     * Retrieve collection all items count
     *
     * @return int
     */
    public function getSize()
    {
        $this->load();
        if (is_null($this->_totalRecords)) {
            $this->_totalRecords = count($this->getAllItems());
        }
        return intval($this->_totalRecords);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->loadData();
            $this->_setIsLoaded();
        }

        if ($this->_pageSize && $this->_curPage) {
            // Calculate the offset based on current page and page size
            $offset = ($this->_curPage - 1) * $this->_pageSize;

            // Get a slice of the items array based on the offset and page size
            $this->_items = array_slice($this->_allItems, $offset, $this->_pageSize);
        } else {
            $this->_items = $this->_allItems;
        }

        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        foreach (Mage::app()->getStores() as $store) {
            if (!$store->getIsActive()) {
                continue;
            }
            Mage::app()->getLocale()->emulate($store->getStoreId());
            $localeCode = Mage::app()->getLocale()->getLocaleCode();

            $modules = Mage::getSingleton('core/translate')->getModulesConfig();

            foreach ($modules as $moduleName => $info) {
                $info = $info->asArray();
                if (!isset($info['files']) || !is_array($info['files'])) {
                    continue;
                }


                foreach ($info['files'] as $fileName) {
                    $file = Mage::getBaseDir('locale') . DS . $localeCode . DS . $fileName;
                    if (!file_exists($file)) {
                        continue;
                    }
                    $parser = new Varien_File_Csv();
                    $parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);

                    foreach ($parser->getDataPairs($file) as $original => $translation) {

                        $item = new Varien_Object([
                            'locale' => $localeCode,
                            'module' => $moduleName,
                            'text' => $original,
                            'translation' => $translation
                        ]);

                        $item->setIdFieldName('id');
                        $item->setId(md5(json_encode($item->getData())));

                        $match = true;
                        if (!empty($this->_filters)) {
                            /** @var Varien_Object $filter */
                            foreach ($this->_filters as $filter) {
                                $f = $filter->getData('field');
                                if (!preg_match($filter->getData('pattern'), $item->getData($f))) {
                                    $match = false;
                                }
                            }
                        }

                        try {
                            if ($match) {
                                $this->addItem($item);
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            }


            Mage::app()->getLocale()->revert();
        }
        return $this;
    }

    /**
     * Adding item to item array
     *
     * @param Varien_Object $item
     * @return  $this
     */
    public function addItem(Varien_Object $item)
    {
        $itemId = $this->_getItemId($item);

        if (!is_null($itemId)) {
            if (isset($this->_allItems[$itemId])) {
                throw new Exception('Item (' . get_class($item) . ') with the same id "' . $item->getId() . '" already exist');
            }
            $this->_allItems[$itemId] = $item;
        } else {
            $this->_addItem($item);
        }
        return $this;
    }

    /**
     * Add item that has no id to collection
     *
     * @param Varien_Object $item
     * @return $this
     */
    protected function _addItem($item)
    {
        $this->_allItems[] = $item;
        return $this;
    }

    public function getAllItems()
    {
        return $this->_allItems;
    }

}

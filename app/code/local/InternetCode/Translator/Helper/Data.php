<?php


class InternetCode_Translator_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function updateMissingTranslationFile(InternetCode_Translator_Model_Missing $model, $translationText)
    {
        $this->updateTranslationFile($model->getLocale(), $model->getModule(), $model->getText(), $translationText);
    }

    /**
     * @param string $locale
     * @param string $module
     * @param string $text
     * @param string $translationText
     * @return void
     * @throws Mage_Core_Exception
     */
    public function updateTranslationFile(string $locale, string $module, string $text, string $translationText)
    {

        foreach (Mage::app()->getStores() as $store) {
            if (self::localeFromStore($store->getId()) == $locale) {
                Mage::app()->getLocale()->emulate($store->getId());
                break;
            }
        }

        $modules = Mage::getSingleton('core/translate')->getModulesConfig();
        $localeCode = Mage::app()->getLocale()->getLocaleCode();

        $saved = false;
        foreach ($modules as $moduleName => $info) {
            /**
             * Filter modules
             */
            if ($moduleName !== $module) {
                continue;
            }

            $info = $info->asArray();
            if (!isset($info['files']) || !is_array($info['files'])) {
                Mage::throwException($moduleName . ' does not have a translation csv file set in config.xml');
            }

            /**
             * Each translation file
             */
            foreach ($info['files'] as $fileName) {

                $file = Mage::getBaseDir('locale') . DS . $localeCode . DS . $fileName;

                $parser = new Varien_File_Csv();
                $parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);

                if (file_exists($file)) {
                    $data = $parser->getDataPairs($file);
                } else {
                    $data = [];
                }

                /**
                 * Append/replace
                 */
                $data[$text] = $translationText;
                $parser->saveData($file, $this->_prepDataPairsForCsv($data));
                $saved = true;
            }

        }
        Mage::app()->getLocale()->revert();
        if (!$saved) {
            Mage::throwException('Translation was not saved. Unknown error.');
        }
    }

    private function _prepDataPairsForCsv($data)
    {
        $newData = [];
        foreach ($data as $k => $v) {
            $newData[] = [$k, $v];
        }
        return $newData;
    }

    public function checkAndInsert(string $module, string $text, $fallback = null, $store_id = 0)
    {
        if (!Mage::getStoreConfigFlag('dev/ic_translate/enabled', $store_id)) {
            return;
        }
        $cache = $this->_getCache();
        $locale = self::localeFromStore($store_id);
        if (!isset($cache[$locale][$module . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $text])) {
            $cache[$locale][$module . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $text] = Mage::getModel('ic_translator/missing')->setData([
                'module' => $module,
                'text' => $text,
                'fallback' => $fallback,
                'locale' => $locale
            ])->save();
            Mage::unregister('ic_translate_cache');
            Mage::register('ic_translate_cache', $cache);
        } else {
            /** @var InternetCode_Translator_Model_Missing $item */
            $item = $cache[$locale][$module . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $text];

            if ($item->getFallback() !== $fallback) {
                $item->setFallback($fallback)->save();
            }
        }
    }

    private function _getCache()
    {
        if (Mage::registry('ic_translate_cache') === null) {
            $cache = [];

            /** @var InternetCode_Translator_Model_Missing $item */
            foreach (Mage::getModel('ic_translator/missing')->getCollection() as $item) {
                $cache[$item->getLocale()][$item->getModule() . Mage_Core_Model_Translate::SCOPE_SEPARATOR . $item->getText()] = $item;
            }
            Mage::register('ic_translate_cache', $cache);
        }
        return Mage::registry('ic_translate_cache');
    }


    public static function localeFromStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            return Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store->getId());
        } elseif (is_string($store) || is_numeric($store)) {
            return Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store);
        }
        Mage::throwException('Unknown store');
    }
}

<?php

class InternetCode_Translator_Model_Translate extends Mage_Core_Model_Translate
{

    /**
     * Return translated string from text.
     *
     * @param string $text
     * @param string $code
     * @return string
     */
    protected function _getTranslatedString($text, $code)
    {

        if (!array_key_exists($code, $this->getData())) {
            $codeArr = explode(self::SCOPE_SEPARATOR, $code);
            $module = $codeArr[0];

            Mage::helper('ic_translator')->checkAndInsert(
                $module,
                $text,
                array_key_exists($text, $this->getData()) ? $this->_data[$text] : null,
                Mage::app()->getStore()->getId()
            );
        }

        return parent::_getTranslatedString($text, $code);
    }
}

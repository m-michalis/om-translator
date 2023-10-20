<?php


class InternetCode_Translator_Adminhtml_Ic_CsvController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('ic_translator/adminhtml_csv'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getResourceModel('ic_translator/csvtranslation_collection');
        $translation = $collection->getItemById($id);

        if (!$translation->getId()) {
            $this->_getSession()->addError(
                Mage::helper('ic_translator')->__('This CSV Translation no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('current_model', $translation);

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('ic_translator/adminhtml_csv_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            // save model
            try {
                Mage::helper('ic_translator')->updateTranslationFile($data['locale'], $data['module'], $data['text'], $data['new_text']);
                $this->_getSession()->addSuccess(
                    Mage::helper('ic_translator')->__('The CSV Translation has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('ic_translator')->__('Unable to save the CSV Translation.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
}

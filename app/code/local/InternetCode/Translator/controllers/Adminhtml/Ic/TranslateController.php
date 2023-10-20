<?php


class InternetCode_Translator_Adminhtml_Ic_TranslateController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('ic_translator/adminhtml_missing'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ic_translator/missing');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('ic_translator')->__('This Missing Translation no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('current_model', $model);

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('ic_translator/adminhtml_missing_edit'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('ic_translator/missing');
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('ic_translator')->__('This Missing Translation no longer exists.')
                );
                $this->_redirect('*/*/index');
                return;
            }

            // save model
            try {
                $tran = $data['new_text'];

                Mage::helper('ic_translator')->updateMissingTranslationFile($model, $tran);
                $model->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('ic_translator')->__('The Missing Translation has been added.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('ic_translator')->__('Unable to save the Missing Translation.'));
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack) {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function clearAllAction()
    {
        Mage::getResourceModel('ic_translator/missing')->truncate();
        $this->_redirect('*/*/');
    }
}

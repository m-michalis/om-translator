<?php


class InternetCode_Translator_Block_Adminhtml_Csv_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * @return InternetCode_Translator_Block_Adminhtml_Csv_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = $this->_getModel();
        $modelTitle = $this->_getModelTitle();
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ]);

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => $this->_getHelper()->__("$modelTitle Information"),
            'class' => 'fieldset-wide',
        ]);

        if ($model && $model->getId()) {

            if ($model->getResource()) {
                $modelPk = $model->getResource()->getIdFieldName();
                $fieldset->addField($modelPk, 'hidden', [
                    'name' => $modelPk,
                    'value' => $model->getId()
                ]);
            }

            $fieldset->addField('locale', 'hidden', [
                'name' => 'locale',
                'value' => $model->getLocale()
            ]);
            $fieldset->addField('module', 'hidden', [
                'name' => 'module',
                'value' => $model->getModule()
            ]);
            $fieldset->addField('text', 'hidden', [
                'name' => 'text',
                'value' => $model->getText()
            ]);


            $fieldset->addField('view_module', 'note', [
                'label' => 'Module',
                'text' => $model->getModule(),
            ]);

            $localeArr = explode('_', $model->getLocale());
            $fieldset->addField('view_locale', 'note', [
                'label' => 'Language',
                'text' => Zend_Locale::getTranslation($localeArr[0], 'language'),
            ]);

            $fieldset->addField('fallback', 'note', [
                'label' => 'Fallback text from another module',
                'text' => '<code style="background-color: #f4f4f4; padding: 5px; display: block; margin: 10px 0;">'.($model->getFallback() ?? '<i>no fallback text</i>').'</code>',
            ]);

            $fieldset->addField('view_text', 'note', [
                'label' => 'Text that needs translation',
                'text' => '<code style="background-color: #f4f4f4; padding: 5px; display: block; margin: 10px 0;">'.$this->escapeHtml($model->getText()).'</code>',
            ]);

            $fieldset->addField('new_text', 'text', [
                'name' => 'new_text',
                'label' => $this->_getHelper()->__('Enter Translation'),
                'value' => $model->getTranslation() ?? $model->getText(),
                'required' => true
            ]);


            $fieldset->addField('view_rules', 'note', [
                'label' => 'Rules',
                'text' => <<<HTML
<div style="font-family: Arial, sans-serif; margin: 20px;">
    <h2 style="color: #333; border-bottom: 2px solid #666; padding-bottom: 10px;">Translation Guidelines</h2>
    
    <p style="color: #555;">When translating, please follow the rules and best practices provided below:</p>

    <h3 style="color: #444;margin-top:20px;">1. Basic Translations</h3>
    <code style="background-color: #f4f4f4; padding: 5px; display: block; margin: 10px 0;">"Original String", "Your Translation Here"</code>

    <h3 style="color: #444;margin-top:20px;">2. Placeholders</h3>
    <p style="color: #555;">Some strings have placeholders like <code>%s</code> or <code>%1$s</code>. Always keep them in your translation as they will be replaced with actual values on the website.</p>
    <p>Examples:</p>
    <ul style="color: #555;">
        <li><code>"You've added %s items to cart"</code> might become "You've added 5 items to cart".</li>
        <li><code>"%1$s bought %2$s items"</code> might become "John bought 3 items".</li>
    </ul>

    <h3 style="color: #444;margin-top:20px;">3. Special Characters</h3>
    <p style="color: #555;">If you see two double quotes <code>""</code> inside a string, it's representing a single double quote in the actual text. Ensure they remain in your translation.</p>
    <code style="background-color: #f4f4f4; padding: 5px; display: block; margin: 10px 0;">"Say "",hello,"" to the world"</code>

    <h3 style="color: #444;margin-top:20px;">4. Handling Commas</h3>
    <p style="color: #555;">For translations with commas, ensure you don't mistakenly add or remove any.</p>
    <code style="background-color: #f4f4f4; padding: 5px; display: block; margin: 10px 0;">"Hello, world!", "Hola, mundo!"</code>

    <h3 style="color: #444;margin-top:20px;">Best Practices:</h3>
    <ul style="color: #555;">
        <li>Always keep placeholders (like <code>%s</code>, <code>%1$s</code>) unchanged in your translations.</li>
        <li>Always ensure you're translating the text within the quotes, and not altering the structure of the CSV.</li>
        <li>Consider the length of translated strings. Some languages might have longer translations that can affect the display on the website.</li>
        <li>Always review your translations in context, if possible, to ensure they make sense in the live environment.</li>
    </ul>
</div>
HTML

            ]);

        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Varien_Object|null
     */
    protected function _getModel()
    {
        return Mage::registry('current_model');
    }

    /**
     * @return string
     */
    protected function _getModelTitle()
    {
        return 'CSV Translation';
    }

    /**
     * @return InternetCode_Translator_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('ic_translator');
    }

}

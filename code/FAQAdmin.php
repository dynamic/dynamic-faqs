<?php

class FAQAdmin extends ModelAdmin
{
    private static $managed_models = array(
        'FAQ',
        'FAQTopic',
    );
    private static $model_importers = array(
        'FAQ' => 'FAQBulkLoader',
    );
    private static $url_segment = 'faqs';
    private static $menu_title = 'FAQs';

    public function getSearchContext()
    {
        $context = parent::getSearchContext();
        $params = $this->request->requestVar('q');

        if ($this->modelClass == 'FAQ') {
            $fields = $context->getFields();

            $fields->removeByName('q[LastEdited]');
            $fields->push(new CheckboxField('q[Last6Months]', 'Records Requiring Update'));
        }

        return $context;
    }

    public function getList()
    {
        $list = parent::getList();

        $params = $this->request->requestVar('q'); // use this to access search parameters

        if ($this->modelClass == 'FAQ' && isset($params['Last6Months']) && $params['Last6Months']) {
            $list = $list->exclude('LastEdited:GreaterThan', date('Y-m-d', strtotime('-6 months')));
        }

        return $list;
    }

    public function getExportFields()
    {
        if ($this->modelClass == 'FAQ') {
            return array(
                'ID' => 'ID',
                'Title' => 'Title',
                'URLSegment' => 'URLSegment',
                'Type' => 'Type',
                'Content' => 'Content',
                'URL' => 'URL',
                'Popularity' => 'Popularity',
                'SilverCloud_ID' => 'SilverCloud_ID',
                'TopicNames' => 'Categories',
                'Keywords' => 'Keywords',
                'ShowInResults' => 'ShowInResults',
            );
        }

        return parent::getExportFields();
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        $gridFieldName = 'FAQ';
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        if ($gridField) {
            $gridField->getConfig()->getComponentByType('GridFieldPrintButton')->setPrintColumns($columns = array(
                'ID' => 'ID',
                'Title' => 'Title',
                'Content' => 'Content',
            ));
        }

        return $form;
    }
}
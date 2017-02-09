<?php

class FAQBulkLoader extends CsvBulkLoader
{
    public $columnMap = array(
        'Title' => 'Title',
        'Type' => 'Type',
        'Content' => '->getContent',
        'Categories' => '->getTopic',
        'Keywords' => 'Keywords',
    );

    public $duplicateChecks = array(
        'SilverCloud_ID',
        'ID',
    );

    // getter functions to use in custom importer methods below
    public function getBoolean($val)
    {
        return $val == 'Y';
    }

    public function getDate($val)
    {
        return date('Y-m-d', strtotime($val));
    }

    public function getTime($val)
    {
        return date('H:i:s', strtotime($val));
    }

    public function getEscape($val)
    {
        $val = str_replace('_x000D_', '', $val);
        return preg_replace("/\r|\n/", '', $val);
    }

    public function getContent(&$obj, $val, $record)
    {
        if ($val) {
            $content = $this->getEscape($val);
            if ($obj->Type == 'Link' && $obj->URL == '') {
                $obj->URL = $content;
            } else {
                $obj->Content = $content;
            }
        }
    }

    public function getTopic(&$obj, $val, $record)
    {
        if ($val) {
            $topics = explode(', ', $this->getEscape($val));
            //SS_Log::log($obj->Title . " parent category = " . $parent, SS_Log::WARN);
            $ct = 1;
            foreach ($topics as $topic) {
                $top = FAQTopic::get()->filter('Title', $topic)->First();
                if (!$top) {
                    $top = FAQTopic::create();
                    $top->Title = $topic;
                    $top->write();
                }
                $obj->Topics()->add($top);
                ++$ct;
            }
        }
    }

    public function getTag(&$obj, $val, $record)
    {
        if ($val) {
            $tags = explode(', ', $this->getEscape($val));
            //SS_Log::log($obj->Title . " parent category = " . $parent, SS_Log::WARN);
            $ct = 1;
            foreach ($tags as $tag) {
                $tg = FAQTag::get()->filter('Title', $tag)->First();
                if (!$tg) {
                    $tg = FAQTag::create();
                    $tg->Title = $tag;
                    $tg->write();
                }
                $obj->Tags()->add($tg);
                ++$ct;
            }
        }
    }

    /**
     * @todo Better messages for relation checks and duplicate detection
     * Note that columnMap isn't used.
     *
     * @param array             $record
     * @param array             $columnMap
     * @param BulkLoader_Result $results
     * @param bool              $preview
     *
     * @return int
     */
    /*
    protected function processRecord($record, $columnMap, &$results, $preview = false)
    {
        $class = $this->objectClass;

        // find existing object, or create new one
        $existingObj = $this->findExistingObject($record, $columnMap);
        $obj = ($existingObj) ? $existingObj : new $class();

        // first run: find/create any relations and store them on the object
        // we can't combine runs, as other columns might rely on the relation being present
        $relations = array();
        foreach ($record as $fieldName => $val) {
            // don't bother querying of value is not set
            if ($this->isNullValue($val)) {
                continue;
            }

            // checking for existing relations
            if (isset($this->relationCallbacks[$fieldName])) {
                // trigger custom search method for finding a relation based on the given value
                // and write it back to the relation (or create a new object)
                $relationName = $this->relationCallbacks[$fieldName]['relationname'];
                if ($this->hasMethod($this->relationCallbacks[$fieldName]['callback'])) {
                    $relationObj = $this->{$this->relationCallbacks[$fieldName]['callback']}($obj, $val, $record);
                } elseif ($obj->hasMethod($this->relationCallbacks[$fieldName]['callback'])) {
                    $relationObj = $obj->{$this->relationCallbacks[$fieldName]['callback']}($val, $record);
                }
                if (!$relationObj || !$relationObj->exists()) {
                    $relationClass = $obj->has_one($relationName);
                    $relationObj = new $relationClass();
                    //write if we aren't previewing
                    if (!$preview) {
                        $relationObj->write();
                    }
                }
                $obj->{"{$relationName}ID"} = $relationObj->ID;
                //write if we are not previewing
                if (!$preview) {
                    $obj->write();
                    $obj->flushCache(); // avoid relation caching confusion
                }
            } elseif (strpos($fieldName, '.') !== false) {
                // we have a relation column with dot notation
                list($relationName, $columnName) = explode('.', $fieldName);
                // always gives us an component (either empty or existing)
                $relationObj = $obj->getComponent($relationName);
                if (!$preview) {
                    $relationObj->write();
                }
                $obj->{"{$relationName}ID"} = $relationObj->ID;

                //write if we are not previewing
                if (!$preview) {
                    $obj->write();
                    $obj->flushCache(); // avoid relation caching confusion
                }
            }
        }

        // second run: save data

        foreach ($record as $fieldName => $val) {
            // break out of the loop if we are previewing
            if ($preview) {
                break;
            }

            // look up the mapping to see if this needs to map to callback
            $mapped = $this->columnMap && isset($this->columnMap[$fieldName]);

            if ($mapped && strpos($this->columnMap[$fieldName], '->') === 0) {
                $funcName = substr($this->columnMap[$fieldName], 2);

                $this->$funcName($obj, $val, $record);
            } elseif ($obj->hasMethod("import{$fieldName}")) {
                $obj->{"import{$fieldName}"}($val, $record);
            } else {
                $obj->update(array($fieldName => $val));
            }
        }

        // write record
        $id = ($preview) ? 0 : $obj->write();

        if ($preview) {
            $id = 0;
        } else {
            $id = $obj->writeToStage('Stage');
            // now publish it
            //$obj->publish("Stage", "Live");
        }

        // @todo better message support
        $message = '';

        // save to results
        if ($existingObj) {
            $results->addUpdated($obj, $message);
        } else {
            $results->addCreated($obj, $message);
        }

        $objID = $obj->ID;

        $obj->destroy();

        // memory usage
        unset($existingObj);
        unset($obj);

        return $objID;
    }
    */
}
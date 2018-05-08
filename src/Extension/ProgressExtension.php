<?php

use SilverStripe\ORM\DataExtension;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Apr 27, 2018 - 10:27:12 AM
 */
class ProgressExtension
        extends DataExtension {

    protected static $cache_progress = [];

    public function getProgress() {
        $cachedProgress = self::cache_name_check($this->owner->ClassName, $this->owner->ID);
        if (isset($cachedProgress)) {
            return $cachedProgress;
        }

        $fields = $this->getImportantFields();

        if (!$fields) {
            return;
        }

        $total = count($fields);
        $done = 0;
        $incomplete = [];
        foreach ($fields as $fieldName => $specOrName) {
            if ($this->IsNotCompletedField($fieldName, $specOrName)) {
                $incomplete[] = $fieldName;
            } else {
                $done++;
            }
        }

        $result = [
            "ObjectID" => "id{$this->owner->ID}",
            "Done" => $done,
            "Total" => $total,
            "Percentage" => ($done / $total) * 100,
            "Incomplete" => $incomplete,
            "IsCompleted" => ($done == $total),
        ];

        return self::cache_name_check($this->owner->ClassName, $this->owner->ID, $result);
    }

    public function IsCompleted() {
        $data = $this->getProgress();

        if ($data && $data['IsCompleted']) {
            return true;
        }
    }

    public function getProgressBar() {
        $data = $this->getProgress();

        if ($data) {
            return $this->owner
                            ->customise($data)
                            ->renderWith('Includes/Progress');
        }
    }

    public function getImportantItems() {
        $items = [];

        foreach ($this->getImportantFields() as $fieldName => $specOrName) {
            if ($this->IsNotCompletedField($fieldName, $specOrName)) {
                $fieldObject = new $specOrName($fieldName);
//
                // Allow fields to opt-out of scaffolding
                if (!$fieldObject) {
                    continue;
                }

                $fieldObject->setTitle($this->owner->fieldLabel($fieldName));
                $fieldObject->setValue($this->owner->$fieldName);

                $items[] = $fieldObject;
            }
        }

        return $items;
    }

    private function getImportantFields() {
        $rawFields = $this->owner->config()->get('progress_fields');
        if (!$rawFields) {
            return;
        }

        // Merge associative / numeric keys
        $fields = [];
        foreach ($rawFields as $key => $value) {
            if (is_int($key)) {
                $key = $value;
            }
            $fields[$key] = $value;
        }
        return $fields;
    }

    private function IsNotCompletedField($fieldName, $specOrName) {
        $dbObject = $this->owner->dbObject($fieldName);
        if ($dbObject && !$dbObject->exists()) {
            return true;
        }

        $relObject = $this->owner->relObject($fieldName);
        if ($relObject && !$relObject->exists()) {
            return true;
        }
    }

    //////// Cache //////// 
    public static function cache_name_check($className, $objectID, $result = null) {
        // This is the name used on the permission cache
        // converts something like 'CanEditType' to 'canedittype'.
        $cacheKey = "$className-$objectID";

        if (isset(self::$cache_progress[$cacheKey])) {
            $cachedValues = self::$cache_progress[$cacheKey];
            return $cachedValues;
        }

        self::$cache_progress[$cacheKey] = $result;

        return self::$cache_progress[$cacheKey];
    }

}

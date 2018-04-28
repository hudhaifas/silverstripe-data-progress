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
        foreach ($fields as $name => $specOrName) {
            if ($this->owner->$name) {
                $done++;
            } else {
                $incomplete[] = $name;
            }
        }

        $result = [
            "Done" => $done,
            "Total" => $total,
            "Percentage" => ($done / $total) * 100,
            "Incomplete" => $incomplete,
            "IsCompleted" => ($done == $total),
        ];

        return self::cache_name_check($this->owner->ClassName, $this->owner->ID, $result);
    }

    public function getProgressBar() {
        return $this->owner
                        ->customise($this->getProgress())
                        ->renderWith('Includes/Progress');
    }

    public function getImportantItems() {
        $items = [];

        foreach ($this->getImportantFields() as $fieldName) {
            if (!$this->owner->$fieldName) {
                $fieldObject = $this->owner->dbObject($fieldName)->scaffoldFormField(null);

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

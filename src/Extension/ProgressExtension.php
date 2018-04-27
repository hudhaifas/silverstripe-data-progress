<?php

use SilverStripe\ORM\DataExtension;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Apr 27, 2018 - 10:27:12 AM
 */
class ProgressExtension
        extends DataExtension {

    public function getProgress() {
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

        if (!$fields) {
            return;
        }

        $total = count($fields);
        $labels = $this->owner->fieldLabels();
        $done = 0;

//        // Localize fields (if possible)
//        foreach ($this->fieldLabels(false) as $name => $label) {
//            // only attempt to localize if the label definition is the same as the field name.
//            // this will preserve any custom labels set in the summary_fields configuration
//            if (isset($fields[$name]) && $name === $fields[$name]) {
//                $fields[$name] = $label;
//            }
//        }

        foreach ($fields as $name => $specOrName) {
            if ($this->owner->$name) {
                $done++;
            }
        }

        return $this->owner
                        ->customise([
                            "Done" => $done,
                            "Total" => $total,
                            "Percentage" => ($done / $total) * 100,
                        ])
                        ->renderWith('Includes/Progress');
    }

}

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
        $fields = $this->owner->config()->get('progress_fields');
        if (!$fields) {
            return 100;
        }

        $num = count($fields);
        $labels = $this->fieldLabels();
        $count = 0;

        foreach ($fields as $name => $specOrName) {
            var_dump($name . ' -- ' . $specOrName);
        }
    }

}

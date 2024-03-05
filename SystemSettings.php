<?php

namespace Piwik\Plugins\VipDetector;

use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\UrlLike;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings {
    public $importUrl;

    protected function init() {
        $this->importUrl = $this->createImportUrlSetting();
    }

    private function createImportUrlSetting() {
        return $this->makeSetting(
            'importUrl',
            $default = "https://austroedit-ranges.sebastian-elisa-pfeifer.eu/all.json",
            FieldConfig::TYPE_STRING,
            function (FieldConfig $field) {
                $field->title = 'Json Source File Download URL';
                $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
                $field->description = 'The URL where the range file is located';
                $field->validators[] = new NotEmpty();
                $field->validators[] = new UrlLike();
            }
        );
    }
}

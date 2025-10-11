<?php

namespace App\Helpers;

class TranslationHelper
{
    public static function getTranslations($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $translations = [];
        $langPath = lang_path($locale);

        if (is_dir($langPath)) {
            $files = glob($langPath . '/*.php');
            foreach ($files as $file) {
                $key = basename($file, '.php');
                $translations[$key] = require $file;
            }
        }

        return $translations;
    }
}

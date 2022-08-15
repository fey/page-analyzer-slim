<?php

namespace Feycot\PageAnalyzer;

class UrlValidator
{
    public static function validate($url): ?string
    {
        if (empty($url)) {
            return 'Url cannot be empty';
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Url should be valid';
        }

        return null;
    }
}

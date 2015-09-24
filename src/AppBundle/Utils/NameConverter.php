<?php

namespace AppBundle\Utils;

class NameConverter
{
    public function toAcronym($string)
    {
        $patterns = [
            '/\p{Lu}/u',
            '/(?<!\p{L})\p{L}/u',
        ];

        $matches = [];

        foreach ($patterns as $pattern) {
            $result = preg_match_all($pattern, $string, $matches);
            if ($result > 0) {
                return mb_strtoupper(implode('', $matches[0]), 'utf-8');
            }
        }

        return '';
    }
}

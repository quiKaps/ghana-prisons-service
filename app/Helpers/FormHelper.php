<?php

namespace App\Helpers;

class FormHelper
{
    /**
     * Normalize a multi-select field with an "other" input.
     *
     * @param array $data The full form data
     * @param string $field The base field name (e.g. 'languages_spoken')
     * @param string $output 'json' or 'array'
     * @return array Modified form data
     */
    public static function mergeWithOther(array $data, string $field, string $output = 'json'): array
    {
        $base = $data[$field] ?? [];

        if (!is_array($base)) {
            $base = [$base];
        }

        $base = array_filter($base, fn($item) => $item !== 'others');

        $otherKey = "{$field}_other";

        if (!empty($data[$otherKey])) {
            $base[] = $data[$otherKey];
        }

        $data[$field] = $output === 'array' ? array_values($base) : json_encode(array_values($base));

        unset($data[$otherKey]);

        return $data;
    }

    // You can add more helpers here later...
}

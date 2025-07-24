<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class EnumHelper {
    /**
     * Get enum values from various columns in a given table.
     */
    public static function getEnumValues($table, $column) {
        $enumValues = DB::select("SHOW COLUMNS FROM $table WHERE Field = '{$column}'")[0]->Type;
        preg_match_all("/'([^']+)'/", $enumValues, $matches);
        $enumOptions = $matches[1];

        return $enumOptions;
    }
    
    public static function getEnum($table, $column)
    {
        $enumValues = DB::select("SHOW COLUMNS FROM $table WHERE Field = '{$column}'")[0]->Type;
        preg_match_all("/'([^']+)'/", $enumValues, $matches);
        $enumOptions = $matches[1];

        // Convert to title case and create key-value pairs
        $formattedData = self::convertToTitleCase($enumOptions);
        $formattedEnum = [];

        foreach ($enumOptions as $value) {
            $formattedEnum[$value] = $formattedData[$value];
        }

        return $formattedEnum;
    }

    public static function convertToTitleCase($data)
    {
        $formattedData = [];

        foreach ($data as $value) {
            // Capitalize each word and replace underscore with space
            $formattedValue = ucwords(str_replace('_', ' ', $value));

            // Save formatted value with original key
            $formattedData[$value] = $formattedValue;
        }

        return $formattedData;
    }
}
<?php

namespace App\Utility;

class Utility
{

    /**
     * Echo a new line in console or browser
     *
     * @return string
     */
    public static function newLine()
    {
        if (php_sapi_name() == 'cli')
            return "\n";

        return "<br>";
    }


    /**
     * Wrap and echo given text with box
     *
     * @param string $text
     * @return string
    */
    public static function box($text)
    {
        echo self::newLine();
        echo "====================================".self::newLine();
        echo $text.self::newLine();
        echo "====================================".self::newLine();
    }

    /**
     * Echo a message
     *
     * @param string $text
     * @return string
     */
    public static function write($text)
    {
        echo "> " . $text . self::newLine();
    }

    /**
     * Convert bytes to precision
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Calculate a percentage
     * @param $current
     * @param $total
     * @return float
     */
    public static function getPercentage($current, $total) {
        return @($current/$total * 100); // Suppress warning division by zero
    }
}
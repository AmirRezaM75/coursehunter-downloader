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
}
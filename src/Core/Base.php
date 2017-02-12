<?php

namespace PhpObjects\Core;

abstract class Base
{

    /**
     * @param int $length
     * @return string
     */
    public static function getUuidMd5( $length = 16 )
    {
        return (string) (new \PhpObjects\Core\UuidMd5($length));
    }

    /**
     * @return string
     */
    public static function getUuidV4()
    {
        return (string) (new \PhpObjects\Core\UuidV4());
    }

    /**
     * @param mixed $var
     * @param string $label
     * @param boolean $echo
     * @return string
     */
    public static function dump( $var, $label = null, $echo = true )
    {
        // format the label
        $label = ($label === null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/ \=\>\n(\s+)/m", " => ", $output);

        if ( PHP_SAPI == 'cli' ) {
            $output = $label . '' . $output . PHP_EOL;
        } else {
            $encode = mb_detect_encoding($output);
            #$string = PhpObjects_PartnerCore_Formatter_ToEncodingMapper::UTF_8()->cast($output);
            $string = ($encode == 'UTF-8' ? $output : utf8_decode($output));
            $output = '<pre>' . $label . htmlentities($string, ENT_QUOTES) . '</pre>';
        }

        if ( $echo ) echo($output);

        return $output;
    }

}

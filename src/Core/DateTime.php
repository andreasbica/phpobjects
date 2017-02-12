<?php

namespace PhpObjects\Core;

class DateTime
{

    /**
     * @param string $value
     * @return bool
     */
    public static function checkDate( $value )
    {
        try {
            if ( strlen($value) < 10 ) throw new \Exception('No Date.');
            $dateTime = new \DateTime($value);

            if ( date('Y-m-d', strtotime($value)) == $dateTime->format('Y-m-d') ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }


    /**
     * @param string $value
     * @return bool
     */
    public static function checkTime( $value )
    {
        try {
            if ( strlen($value) < 8 ) throw new \Exception('No Time.');
            $dateTime = new \DateTime($value);

            if ( date('H:i:s', strtotime($value)) == $dateTime->format('H:i:s') ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }


    /**
     * @param string $value
     * @return bool
     */
    public static function checkDateTime( $value )
    {
        try {
            if ( strlen($value) < 19 ) throw new \Exception('No DateTime.');
            $dateTime = new \DateTime($value);

            if ( date('Y-m-d H:i:s', strtotime($value)) == $dateTime->format('Y-m-d H:i:s') ) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

}

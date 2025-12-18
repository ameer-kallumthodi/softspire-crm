<?php

namespace App\Helpers;

class CountriesHelper
{
    /**
     * Get list of country codes with country names
     */
    public static function getCountryCode()
    {
        return [
            '91'    => 'INDIA',
            '971'   => 'UNITED ARAB EMIRATES',
            '974'   => 'QATAR',
            '1'     => 'CANADA / UNITED STATES',
            '965'   => 'KUWAIT',
            '966'   => 'SAUDI ARABIA',
            '973'   => 'BAHRAIN',
            '33'    => 'FRANCE',
            '34'    => 'SPAIN',
            '39'    => 'ITALY',
            '44'    => 'UNITED KINGDOM',
            '46'    => 'SWEDEN',
            '48'    => 'POLAND',
            '49'    => 'GERMANY',
            '61'    => 'AUSTRALIA',
            '64'    => 'NEW ZEALAND',
            '353'   => 'IRELAND',
            '358'   => 'FINLAND',
            '370'   => 'LITHUANIA',
            '968'   => 'OMAN',
        ];
    }

    /**
     * Get country name by code
     */
    public static function getName($code)
    {
        $countries = self::getCountryCode();
        return $countries[$code] ?? $code;
    }

    /**
     * Get country codes
     */
    public static function getCodes()
    {
        return array_keys(self::getCountryCode());
    }
}


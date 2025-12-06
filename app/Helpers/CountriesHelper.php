<?php

namespace App\Helpers;

class CountriesHelper
{
    /**
     * Get list of countries
     */
    public static function getAll()
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PL' => 'Poland',
            'IE' => 'Ireland',
            'PT' => 'Portugal',
            'GR' => 'Greece',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'RO' => 'Romania',
            'BG' => 'Bulgaria',
            'HR' => 'Croatia',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'LT' => 'Lithuania',
            'LV' => 'Latvia',
            'EE' => 'Estonia',
            'LU' => 'Luxembourg',
            'MT' => 'Malta',
            'CY' => 'Cyprus',
            'IN' => 'India',
            'CN' => 'China',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'SG' => 'Singapore',
            'MY' => 'Malaysia',
            'TH' => 'Thailand',
            'ID' => 'Indonesia',
            'PH' => 'Philippines',
            'VN' => 'Vietnam',
            'NZ' => 'New Zealand',
            'ZA' => 'South Africa',
            'EG' => 'Egypt',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'IL' => 'Israel',
            'TR' => 'Turkey',
            'RU' => 'Russia',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PE' => 'Peru',
        ];
    }

    /**
     * Get country name by code
     */
    public static function getName($code)
    {
        $countries = self::getAll();
        return $countries[$code] ?? $code;
    }

    /**
     * Get country codes
     */
    public static function getCodes()
    {
        return array_keys(self::getAll());
    }
}


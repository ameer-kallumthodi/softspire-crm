<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    /**
     * Format phone number
     */
    public static function format($phone, $countryCode = 'US')
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return '';
        }

        // Format based on country code
        switch ($countryCode) {
            case 'US':
            case 'CA':
                if (strlen($phone) == 10) {
                    return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
                }
                break;
            case 'GB':
                if (strlen($phone) == 10) {
                    return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
                }
                break;
        }

        return $phone;
    }

    /**
     * Validate phone number
     */
    public static function validate($phone, $countryCode = 'US')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        switch ($countryCode) {
            case 'US':
            case 'CA':
                return strlen($phone) == 10;
            case 'GB':
                return strlen($phone) >= 10 && strlen($phone) <= 11;
            default:
                return strlen($phone) >= 7 && strlen($phone) <= 15;
        }
    }

    /**
     * Get country code from phone number
     */
    public static function getCountryCode($phone)
    {
        // Simple detection - can be enhanced
        if (preg_match('/^\+1/', $phone)) {
            return 'US';
        }
        if (preg_match('/^\+44/', $phone)) {
            return 'GB';
        }
        return 'US';
    }
}


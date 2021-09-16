<?php


namespace App\Utils;


class Uptime
{
    public static function getCategoryImage($key, $index)
    {
        $images = [
            ['tablets.png', 'redux.png', 'power.png'],
            ['coaching-brown.png', 'meetings-brown.png', 'power-brown.png'],
            ['sales-award.png', 'training-award.png', 'ops-award.png'],
        ];

        return $images[$key % 3][$index % 3];
    }

    public static function getCategoryColor($index)
    {
        $colors = ['first_color', 'second_color', 'third_color', 'forth_color'];

        return $colors[$index % 4];
    }

    public static function formatPhoneNumber($number)
    {
        // Skip further processing, value is a valid email
        if (filter_var($number, FILTER_VALIDATE_EMAIL)) {
            return $number;
        }

        // Remove any non-digits
        $number = preg_replace('/[^\d]/','', $number);

        // Trim leading '1' from the number if more than 10 characters are present
        $number = strlen($number) > 10 ? ltrim($number, '1') : $number;

        return $number;
    }
}
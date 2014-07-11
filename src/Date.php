<?php
namespace Ptilz;

class Date {
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const MYSQL_DATE_FORMAT = 'Y-m-d';

    /**
     * @param int $timestamp The optional timestamp parameter is an integer Unix timestamp that defaults to the current local time if a timestamp is not given. In other words, it defaults to the value of time().
     *
     * @return string A string suitable for inserting into a DATETIME field in a MySQL database
     */
    public static function unixToDateTime($timestamp = null) {
        if($timestamp === null) $timestamp = time();
        return date(self::MYSQL_DATETIME_FORMAT, $timestamp);
    }

    /**
     * @param string $str A date/time string.
     * @param int $timestamp The timestamp which is used as a base for the calculation of relative dates.
     *
     * @return string A string suitable for inserting into a DATETIME field in a MySQL database
     * @see http://ca3.php.net/manual/en/function.strtotime.php
     */
    public static function strToDateTime($str, $timestamp = null) {
        if($timestamp === null) $timestamp = time();
        return self::unixToDateTime(strtotime($str, $timestamp));
    }

    /**
     * @param string $datetime MySQL DateTime
     * @return int Unix timestamp
     */
    public static function dateTimeToUnix($datetime) {
        list($date, $time) = explode(' ', $datetime);
        list($year, $month, $day) = explode('-', $date);
        list($hour, $minute, $second) = explode(':', $time);
        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * @param string $format The format of the outputted date string.
     * @param string $datetime MySQL DateTime
     *
     * @return bool|string
     * @see http://ca3.php.net/manual/en/function.date.php
     */
    public static function formatDateTime($format, $datetime) {
        return date($format, self::dateTimeToUnix($datetime));
    }
}
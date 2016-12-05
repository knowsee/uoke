<?php
namespace Helper;
use DateTimeZone;

class Date extends \DateTime {

    const FORMAT_DEFAULT = 'Y-m-d H:i:s';
    const FORMAT_YEAR_TO_DAY = 'Y-m-d';
    const FORMAT_DAY_TIME = 'H:i:s';
    const FORMAT_AI_TIME = 'A';

    public function __construct($time, DateTimeZone $timezone) {
        parent::__construct($time, $timezone);
    }

    public static function formatTime(int $unixTime, string $format = '') {
        if(!$format) $format = CONFIG('timeFormat/default');
        if($format == self::FORMAT_AI_TIME) {
            return self::AITime($unixTime);
        } else {
            return self::createFromFormat($format, date(self::ISO8601, $unixTime));
        }
    }

    private static function AITime(int $unixTime) {
        $getDiff = $unixTime-UNIXTIME;
        if($getDiff >= 0) {
            $mixZero = 'before';
        } else {
            $mixZero = 'after';
        }
        $messageCode = self::diffTimeMessage(abs($getDiff));
        if($messageCode == 0) {
            return self::createFromFormat(self::FORMAT_DEFAULT, date(self::ISO8601, $unixTime));
        } else {
            return str_replace(array('@time', '@msg'), array(CONFIG('timeFormat/lang/'.$messageCode), CONFIG('timeFormat/lang/'.$mixZero)), CONFIG('timeFormat/AiString'));
        }
    }

    private static function diffTimeMessage(int $time) {
        $timePath = CONFIG('timeFormat/AiDiff');
        return array_filter($timePath, function($timePath) use($time) {
            if($timePath*60 < $time) {
                return $timePath*60;
            } else {
                return 0;
            }
        });
    }

}
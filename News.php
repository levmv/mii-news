<?php declare(strict_types=1);

namespace mii;

use mii\util\Date;
use mii\util\Text;
use mii\util\UTF8;

class News
{

    public static function midnight(int $date): int
    {
        return $date - ($date % 86400) - 3600*3;
    }


    private static ?int $now = null;
    private static ?int $today = null;
    private static ?int $yesterday = null;
    private static ?string $thisYear = null;

    const HOURS = ['час', 'часа', 'часов'];
    const MINUTES = ['минуту', 'минуты', 'минут'];

    public static function age(int $date): string
    {
        self::$now ??= \time();

        $offset = abs(self::$now - $date);

        if ($offset <= Date::MINUTE) {
            $span = 'только что';
        } elseif ($offset < (Date::HOUR * 2)) {
            $minutes = round($offset / 60);
            $span = $minutes . ' ' . Text::decl((int)$minutes, self::MINUTES) . ' назад';
        } elseif ($offset < Date::DAY) {
            $hours = floor($offset / 3600);
            $span = $hours . ' ' . Text::decl((int)$hours, self::HOURS);
        } else {
            $days = floor($offset / 86400);
            $hours = floor($offset % 86400 / 3600);

            $span = $days . ' ' . Text::decl((int)$days, ['день', 'дня', 'дней']) . ' ' .
                $hours . ' ' . Text::decl((int)$hours, self::HOURS);
        }

        return $span;
    }

    public static function date(int $timestamp): string
    {
        self::$now ??= \time();
        self::$today ??= \mktime(0, 0, 0);

        $offset = abs(self::$now - $timestamp);

        if($timestamp > self::$now) {
            return static::futureNewsDate($offset, $timestamp);
        }

        if ($offset <= Date::MINUTE) {
            return 'только что';
        } elseif ($offset < Date::HOUR-60*7) {
            $minutes = (int) round($offset / 60);
            return "$minutes " . Text::decl($minutes, self::MINUTES) . ' назад';
        } elseif ($offset < Date::HOUR+60*7) {
            return 'час назад';
        }

        self::$yesterday ??= \strtotime('yesterday');
        $date = '';
        if ($timestamp >= self::$today) {
            $date = 'сегодня';
        } elseif ($timestamp >= self::$yesterday) {
            $date = 'вчера';
        } else {
            self::$thisYear ??= date('Y');
            if (date('Y', $timestamp) !== self::$thisYear) {
                $date = Date::strftime('%e %B %Y', $timestamp);
            } else {
                $date = Date::strftime('%e %B', $timestamp);
            }
        }
        return $date . ' в ' . date('H:i', $timestamp);
    }

    protected static function futureNewsDate(int $offset, int $timestamp): string
    {
        if ($offset <= Date::MINUTE) {
            return 'через секунды';
        } elseif ($offset < Date::HOUR-60*7) {
            $minutes = (int) round($offset / 60);
            return "через $minutes " . Text::decl($minutes, self::MINUTES);
        } elseif ($offset < Date::HOUR+60*7) {
            return 'через час';
        }
        if($timestamp <= strtotime('tomorrow')) {
            $span = 'сегодня';
        } else {
            if($timestamp <= strtotime('tomorrow+1 day')) {
                $span = 'завтра';
            } else {
                $span = Date::intl('d MMMM')->format($timestamp);
            }
        }
        return $span . date(' в H:i', $timestamp);
    }


    public static function clearVkCache(string $url, string $token)
    {
        $ch = curl_init();

        curl_setopt($ch, \CURLOPT_URL, 'https://api.vk.com/method/pages.clearCache');
        curl_setopt($ch, \CURLOPT_POST, 1);
        curl_setopt($ch, \CURLOPT_POSTFIELDS, http_build_query([
            'access_token' => $token,
            'v' => '5.95',
            'url' => $url,
        ]));
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        curl_close($ch);
    }


    public static function getPercentile(int $percentile, array $array)
    {
        sort($array);
        $index = ($percentile / 100) * (count($array) - 1);

        return $index == floor($index)
            ? ($array[$index - 1] + $array[$index]) / 2
            : $array[floor($index)];
    }

    public static function yaEscape(string $str): string
    {
        return htmlspecialchars(UTF8::strip4b($str), ENT_QUOTES|ENT_DISALLOWED|ENT_XML1, 'UTF-8', false);
    }


}
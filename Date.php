<?php declare(strict_types=1);

namespace mii\news;

use mii\util\Text;
use mii\util\UTF8;

final class Date extends \mii\util\Date
{
    public static function midnight(int $date): int
    {
        return $date - ($date % 86400) - 3600 * 3;
    }

    private static ?int $now = null;
    private static ?int $yesterday = null;

    public const HOURS = ['час', 'часа', 'часов'];
    public const MINUTES = ['минуту', 'минуты', 'минут'];

    public static function age(int $date): string
    {
        self::$now ??= \time();

        $offset = abs(self::$now - $date);

        if ($offset <= self::MINUTE) {
            $span = 'только что';
        } elseif ($offset < (self::HOUR * 2)) {
            $minutes = round($offset / 60);
            $span = $minutes . ' ' . Text::decl((int)$minutes, self::MINUTES) . ' назад';
        } elseif ($offset < self::DAY) {
            $hours = floor($offset / 3600);
            $span = $hours . ' ' . Text::decl((int)$hours, self::HOURS);
        } elseif ($offset < self::DAY * 7) {
            $days = floor($offset / 86400);
            $hours = floor($offset % 86400 / 3600);

            $span = $days . ' ' . Text::decl((int)$days, ['день', 'дня', 'дней']) . ' ' .
                $hours . ' ' . Text::decl((int)$hours, self::HOURS);
        } else {
            $days = floor($offset / 86400);
            return $days . ' ' . Text::decl((int)$days, ['день', 'дня', 'дней']);
        }

        return $span;
    }

    public static function newsDate(int $timestamp)
    {

        [$date, $time] = self::baseNewsDate($timestamp);

        if ($time) {
            return "$date в $time";
        }

        return $date;
    }

    public static function baseNewsDate(int $timestamp): array
    {
        self::$now ??= \time();

        $offset = abs(self::$now - $timestamp);

        if ($timestamp > self::$now) {
            return self::futureNewsDate($offset, $timestamp);
        }

        if ($offset <= self::MINUTE) {
            return ['только что', ''];
        } elseif ($offset < self::HOUR - 60 * 7) {
            $minutes = (int)round($offset / 60);
            return ["$minutes " . Text::decl($minutes, self::MINUTES) . ' назад', ''];
        } elseif ($offset < self::HOUR + 60 * 7) {
            return ['час назад', ''];
        }

        self::$today ??= \mktime(0, 0, 0);
        self::$yesterday ??= \strtotime('yesterday');
        $date = '';
        if ($timestamp >= self::$today) {
            $date = 'сегодня';
        } elseif ($timestamp >= self::$yesterday) {
            $date = 'вчера';
        } else {
            self::$thisYear ??= \mktime(0, 0, 0, 1, 1);
            $date = self::intl($timestamp >= self::$thisYear ? 'd MMMM' : 'd MMMM YYYY')->format($timestamp);
        }
        return [$date, date('H:i', $timestamp)];
    }

    protected static function futureNewsDate(int $offset, int $timestamp): array
    {
        if ($offset <= self::MINUTE) {
            return ['через секунды', ''];
        } elseif ($offset < self::HOUR - 60 * 7) {
            $minutes = (int)round($offset / 60);
            return ["через $minutes " . Text::decl($minutes, self::MINUTES), ''];
        } elseif ($offset < self::HOUR + 60 * 7) {
            return ['через час', ''];
        }
        if ($timestamp <= strtotime('tomorrow')) {
            $span = 'сегодня';
        } else {
            if ($timestamp <= strtotime('tomorrow+1 day')) {
                $span = 'завтра';
            } else {
                $span = self::intl('d MMMM')->format($timestamp);
            }
        }
        return [$span, date('H:i', $timestamp)];
    }

}
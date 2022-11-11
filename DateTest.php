<?php declare(strict_types=1);

namespace miit;

use mii\news\Date;
use mii\web\App;

class DateTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        new App([
            'app' => [
                'locale' => 'ru_RU.UTF-8'
            ]
        ]);
        date_default_timezone_set('Europe/Moscow');
    }


    public function testAge() {
        $this->assertEquals('только что', Date::age(time()-45));
        $this->assertEquals('2 часа', Date::age(time()-60*60*2));
        $this->assertEquals('1 день 1 час', Date::age(time()-60*60*25));
        $this->assertEquals('5 дней 1 час', Date::age(time()-60*60*24*5-3600));
        $this->assertEquals('30 дней', Date::age(time()-60*60*24*30));
    }

    public function testNewsDate()
    {
        $this->assertEquals('только что', Date::newsDate(time()-45));
        $this->assertEquals('через секунды', Date::newsDate(time()+45));

        $this->assertEquals('50 минут назад', Date::newsDate(time()-60*50));
        $this->assertEquals('через 50 минут', Date::newsDate(time()+60*50));

        $this->assertEquals('час назад', Date::newsDate(time()-60*55));
        $this->assertEquals('час назад', Date::newsDate(time()-60*65));
        $this->assertEquals('через час', Date::newsDate(time()+60*55));
        $this->assertEquals('через час', Date::newsDate(time()+60*65));

        $this->assertEquals('сегодня в '.date('H:i', time()-60*60*3), Date::newsDate(time()-60*60*3));
        $this->assertEquals('завтра в '.date('H:i', time()+60*60*24), Date::newsDate(time()+60*60*24));

        $this->assertEquals('вчера в '.date('H:i', time()-60*60*25), Date::newsDate(time()-60*60*25));
        $this->assertEquals('завтра в '.date('H:i', time()+60*60*24), Date::newsDate(time()+60*60*24));


        $date = strtotime('first day of January this year');
        $this->assertEquals("1 января в ".date('H:i', $date), Date::newsDate($date));

        $date = strtotime('first day of December last year');
        $year = date('Y', $date);
        $this->assertEquals("1 декабря $year в ".date('H:i', $date), Date::newsDate($date));
    }

    public function testDate()
    {
        $this->assertEquals(['только что',''], Date::baseNewsDate(time()-45));
        $this->assertEquals(['через секунды',''], Date::baseNewsDate(time()+45));

        $this->assertEquals(['50 минут назад',''], Date::baseNewsDate(time()-60*50));
        $this->assertEquals(['через 50 минут',''], Date::baseNewsDate(time()+60*50));

        $this->assertEquals(['час назад',''], Date::baseNewsDate(time()-60*55));
        $this->assertEquals(['час назад',''], Date::baseNewsDate(time()-60*65));
        $this->assertEquals(['через час',''], Date::baseNewsDate(time()+60*55));
        $this->assertEquals(['через час',''], Date::baseNewsDate(time()+60*65));

        $this->assertEquals(['сегодня', date('H:i', time()-60*60*3)], Date::baseNewsDate(time()-60*60*3));
        $this->assertEquals(['завтра', date('H:i', time()+60*60*24)], Date::baseNewsDate(time()+60*60*24));

        $this->assertEquals(['вчера', date('H:i', time()-60*60*25)], Date::baseNewsDate(time()-60*60*25));
        $this->assertEquals(['завтра', date('H:i', time()+60*60*24)], Date::baseNewsDate(time()+60*60*24));
    }

    /*public function testEscape() {
        $this->assertEquals('&apos;&apos; &lt;&gt; “А”', Date::yaEscape("'🙂' <> “А”"));
    }*/
}

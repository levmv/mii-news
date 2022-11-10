<?php declare(strict_types=1);

namespace miit\util;

use mii\News;
use mii\web\App;

class NewsTest extends \PHPUnit\Framework\TestCase
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
        $this->assertEquals('только что', News::age(time()-45));
        $this->assertEquals('2 часа', News::age(time()-60*60*2));
        $this->assertEquals('1 день 1 час', News::age(time()-60*60*25));
    }

    public function testNewsDate()
    {
        $this->assertEquals('только что', News::date(time()-45));
        $this->assertEquals('через секунды', News::date(time()+45));

        $this->assertEquals('50 минут назад', News::date(time()-60*50));
        $this->assertEquals('через 50 минут', News::date(time()+60*50));

        $this->assertEquals('час назад', News::date(time()-60*55));
        $this->assertEquals('час назад', News::date(time()-60*65));
        $this->assertEquals('через час', News::date(time()+60*55));
        $this->assertEquals('через час', News::date(time()+60*65));

        $this->assertEquals('сегодня в '.date('H:i', time()-60*60*3), News::date(time()-60*60*3));
        $this->assertEquals('завтра в '.date('H:i', time()+60*60*3), News::date(time()+60*60*3));

        $this->assertEquals('вчера в '.date('H:i', time()-60*60*25), News::date(time()-60*60*25));
        $this->assertEquals('завтра в '.date('H:i', time()+60*60*24), News::date(time()+60*60*24));
    }

    public function testEscape() {
        $this->assertEquals('&apos;&apos; &lt;&gt; “А”', News::yaEscape("'🙂' <> “А”"));
    }

    /*

    public function testBase()
    {
        $this->assertEquals('', Url::base());
        $this->assertEquals('http://test.com', Url::base(true));
        $this->assertEquals('https://test.com', Url::base('https'));
        $this->assertEquals('//test.com', Url::base('//'));

        \Mii::$app->base_url = '/base';

        $this->assertEquals('/base', Url::base());
        $this->assertEquals('http://test.com/base', Url::base(true));
        $this->assertEquals('https://test.com/base', Url::base('https'));
        $this->assertEquals('//test.com/base', Url::base('//'));
    }

    public function testCurrent()
    {
        \Mii::$app->request->setUri('');
        $this->assertEquals('/', Url::current());

        \Mii::$app->request->setUri('/');
        $this->assertEquals('/', Url::current());

        \Mii::$app->request->setUri('/test');
        $this->assertEquals('/test', Url::current());
    }*/
}

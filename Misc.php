<?php declare(strict_types=1);

namespace mii\news;

use mii\util\UTF8;

final class Misc extends \mii\util\Misc
{

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


    public static function yaEscape(string $str): string
    {
        return htmlspecialchars(UTF8::strip4b($str), ENT_QUOTES | ENT_DISALLOWED | ENT_XML1, 'UTF-8', false);
    }
}
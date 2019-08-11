<?php
namespace Imi\Smarty\Test;

use Yurun\Util\HttpRequest;
use PHPUnit\Framework\TestCase;

class SmartyTest extends TestCase
{
    protected $host = 'http://127.0.0.1:13000/';

    public function testSmarty()
    {
        $get = [
            'time'  =>  (string)microtime(true),
            'get'   =>  'imi',
        ];
        $post = [
            'time'  =>  (string)microtime(true),
            'post'   =>  'imi',
        ];
        $cookies = [
            'a' =>  1,
            'b' =>  2,
        ];

        $http = new HttpRequest;
        $response = $http->cookies($cookies)->post($this->host . 'test?' . http_build_query($get), $post);
        $responseContent = $response->body();
        $this->assertEquals($this->buildResponseContent($get, $post, $cookies), $responseContent);
    }

    private function buildResponseContent($get, $post, $cookies)
    {
        $content = [
            'CONTENT:',
            'imi nb',
        ];
        $content[] = 'GET:';
        foreach($get as $k => $v)
        {
            $content[] = $k . ':' . $v;
        }
        $content[] = 'POST:';
        foreach($post as $k => $v)
        {
            $content[] = $k . ':' . $v;
        }
        $content[] = 'COOKIE:';
        foreach($cookies as $k => $v)
        {
            $content[] = $k . ':' . $v;
        }
        $request = array_merge($get, $post, $cookies);
        $content[] = 'REQUEST:';
        foreach($request as $k => $v)
        {
            $content[] = $k . ':' . $v;
        }
        $content[] = '';
        return implode(PHP_EOL, $content);
    }
}

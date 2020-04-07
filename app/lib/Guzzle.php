<?php


namespace Libs;


use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;

class Guzzle
{

    /**
     * 建立客户端
     * @param string $method
     * @param $url
     * @param array $option
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function initClient(string $method, $url, $option = [])
    {
        $client  = new Client();
        $postRaw = $option['raw'] ?? 'form_params';

        switch ($method) {
            case "POST":
                $res = $client->request('POST', $url, [
                    $postRaw => $option['params'] ?? null,
                    'verify' => false,
                ]);
                break;
            case "GET":
                $res = $client->request('GET', $url);
                break;
            case "DELETE":
                $res = $client->request('DELETE', $url);
                break;
            case "PUT":
            case "PATCH":
                $res = $client->request('PUT', $url, [
                    $postRaw => $option['params'],
                    'verify' => false,
                ]);
                break;
        }
        $result = \GuzzleHttp\json_decode($res->getBody(), true);
        return $result;
    }

    public static function get($url)
    {
        $res = self::initClient("GET", $url);
        return $res;
    }

    public static function post($url, $option = ['raw' => 'body'])
    {
        $res = self::initClient("POST", $url, $option);
        return $res;
    }

    public static function postAsync($url, $params = [], $paramType = 'json')
    {
        $client    = new Client();
        $promises  = [];
        $success   = [];
        $fail      = [];
        $responses = [];
        if (!empty($params) && is_array($params)) {
            foreach ($params as $param) {
                $promises[] = $client->postAsync($url, [$paramType => $param])->then(function (ResponseInterface $response) use (&$responses, &$success, &$fail, $param) {
                    if ($response->getStatusCode() == 200) {
                        // Set to OK if we received a 200
                        $success[]   = $param;
                        $responses[] = [
                            'param'   => $param,
                            'respone' => $response->getBody()->getContents()
                        ];
                    } else {
                        $fail[] = $param;
                    }
                }, function () use (&$fail, $param) {
                    $fail[] = $param;
                });
            }
        }
//        // Wait for all requests to complete
        Promise\unwrap($promises);
        return $responses;
    }

    public static function headAsync($urls)
    {
        $client   = new Client();
        $promises = [];
        $result   = [];
        $fail     = [];
        foreach ($urls as $url) {
            $promises[] = $client->headAsync($url, [
                'allow_redirects' => [
                    'max'             => 10,        // allow at most 10 redirects.
                    'track_redirects' => true
                ]
            ])->then(function (ResponseInterface $response) use (&$result, $url) {
                $status = $response->getStatusCode();
                if ($status == 200) {
                    $resUrl = $response->getHeader('X-Guzzle-Redirect-History')[0] ?? $url;
                } else {
                    $resUrl = 'request fail';
                }
                $result[] = [
                    'param'   => $url,
                    'respone' => [
                        'code' => $response->getStatusCode(),
                        'url'  => $resUrl
                    ]
                ];
            }, function () use (&$fail, $url, &$result) {
                $fail[]   = $url;
                $result[] = [
                    'param'   => $url,
                    'respone' => [
                        'code' => 404,
                        'url'  => $url
                    ]
                ];
            });
        }
        Promise\unwrap($promises);
        return $result;

    }

}

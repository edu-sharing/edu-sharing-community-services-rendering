<?php
require_once(dirname(__FILE__) . '/../../../conf.inc.php');
require_once(dirname(__FILE__) . '/../../../vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;

class GuzzleHelper {
    private static function addProxyCallback() {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $helper = new ProxyHelper($request->getUri());
                $config = $helper->getProxyConfig();
                if($config) {
                    $options['proxy'] = $config;
                }
                return $handler($request, $options);
            };
        };
    }

    private static function addTracing() {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                foreach(getallheaders() as $key => $value) {
                    if(strcasecmp(substr($key, 0, strlen("X-B3-")), "X-B3-") == 0 ||
                        strcasecmp(substr($key, 0, strlen("X-OT-")), "X-OT-") == 0 ||
                        strcasecmp($key, "X-Request-Id") == 0 ||
                        strcasecmp($key, "X-Client-Trace-Id") == 0
                    ) {
                        $request = $request->withAddedHeader($key, $value);
                    }
                }
                return $handler($request, $options);
            };
        };
    }
    public static function getClient() {
        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);
        $stack->push(GuzzleHelper::addProxyCallback());
        $stack->push(GuzzleHelper::addTracing());
        return new Client(['handler' => $stack]);
    }
}
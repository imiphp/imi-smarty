<?php
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

use Imi\App;

App::setLoader($loader);

/**
 * 开启服务器
 *
 * @return void
 */
function startServer()
{
    function checkHttpServerStatus()
    {
        $serverStarted = false;
        for($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            if('pong' === @file_get_contents('http://127.0.0.1:13000/ping'))
            {
                $serverStarted = true;
                break;
            }
        }
        return $serverStarted;
    }
    
    $servers = [
        'HttpServer'    =>  [
            'start'         => dirname(__DIR__) . '/example/bin/start.sh',
            'stop'          => dirname(__DIR__) . '/example/bin/stop.sh',
            'checkStatus'   => 'checkHttpServerStatus',
        ],
    ];
    
    foreach($servers as $name => $options)
    {
        // start server
        $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
        echo "Starting {$name}...", PHP_EOL;
        echo `{$cmd}`, PHP_EOL;
    
        register_shutdown_function(function() use($name, $options){
            // stop server
            $cmd = $options['stop'];
            echo "Stoping {$name}...", PHP_EOL;
            echo `{$cmd}`, PHP_EOL;
            echo "{$name} stoped!", PHP_EOL;
        });
    
        if(($options['checkStatus'])())
        {
            echo "{$name} started!", PHP_EOL;
        }
        else
        {
            throw new \RuntimeException("{$name} start failed");
        }
    }
}

startServer();

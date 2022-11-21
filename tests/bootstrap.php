<?php

declare(strict_types=1);

use function Imi\ttyExec;

require \dirname(__DIR__) . '/vendor/autoload.php';

function checkHttpServerStatus(): bool
{
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        try
        {
            $context = stream_context_create(['http' => ['timeout' => 1]]);
            $body = @file_get_contents('http://127.0.0.1:13456/ping', false, $context);
            if ('pong' === $body)
            {
                return true;
            }
        }
        catch (ErrorException $e)
        {
        }
    }

    return false;
}

/**
 * 开启服务器.
 */
function startServer(): void
{
    $servers = [
        'HttpServer'    => [
            'start'         => \dirname(__DIR__) . '/example/bin/start.sh',
            'stop'          => \dirname(__DIR__) . '/example/bin/stop.sh',
            'checkStatus'   => 'checkHttpServerStatus',
        ],
    ];

    foreach ($servers as $name => $options)
    {
        // start server
        $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
        echo "Starting {$name}...", \PHP_EOL;
        echo shell_exec("{$cmd}"), \PHP_EOL;

        register_shutdown_function(static function () use ($name, $options) {
            // stop server
            $cmd = $options['stop'];
            echo "Stoping {$name}...", \PHP_EOL;
            echo shell_exec("{$cmd}"), \PHP_EOL;
            echo "{$name} stoped!", \PHP_EOL;
        });

        if (($options['checkStatus'])())
        {
            echo "{$name} started!", \PHP_EOL;
        }
        else
        {
            throw new \RuntimeException("{$name} start failed");
        }
    }
    register_shutdown_function(static function () {
        echo 'check ports...', \PHP_EOL;
        ttyExec(\PHP_BINARY . ' ' . __DIR__ . '/bin/checkPorts.php');
    });
}

startServer();

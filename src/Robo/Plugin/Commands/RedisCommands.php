<?php

declare(strict_types=1);

namespace MyProject\Robo\Plugin\Commands;

use Predis\Client;
use Robo\Tasks;

/**
 * Provides commands for Redis cache backend.
 */
class RedisCommands extends Tasks
{

    /**
     * Flushes the Redis backend.
     *
     * @command redis:flush-all
     */
    public function flushAll(): void
    {
        if ($this->useRedisExtension()) {
            $client = new \Redis();
            $port = getenv('REDIS_PORT') ? (int) getenv('REDIS_PORT') : 6379;
            $client->connect(getenv('REDIS_HOST'), $port);
            if (!empty(getenv('REDIS_PASSWORD'))) {
                $client->auth(getenv('REDIS_PASSWORD'));
            }
        }
        elseif ($this->usePredisLibrary()) {
            $parameters = array_filter([
                'host' => getenv('REDIS_HOST'),
                'port' => getenv('REDIS_PORT'),
                'password' => getenv('REDIS_PASSWORD'),
            ]);
            $client = new Client($parameters);
        } else {
            throw new \Exception('Either PHP Redis extension or predis/predis should be inetslled.');
        }
        $client->flushall();
        $this->say('Redis backend flushed');
    }

    private function useRedisExtension(): bool
    {
        return class_exists(\Redis::class);
    }

    private function usePredisLibrary(): bool
    {
        return class_exists(Client::class);
    }
}

<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * A singleton class responsible for managing a Redis client instance using the Predis library.
 * This class ensures the Redis client is properly configured and available for use throughout the application.
 */
final class RedisClient
{
    static private $instance = null;
    private \Predis\Client $client;

    public static function getInstance(): self {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        $this->client = $this->createClient();
        $this->healthCheck($this->client);
    }

    /**
     * Retrieves the existing Predis client instance, verifying its connection via a ping.
     * If the current client is unavailable or fails the connection test, a new client
     * instance is created and validated before being returned.
     *
     * @return \Predis\Client The operational Predis client instance.
     */
    public function getClient(): \Predis\Client {
        try {
            $this->healthCheck($this->client);
            return $this->client;
        } catch (\Throwable $e) {
            $this->client = $this->createClient();
            $this->healthCheck($this->client);

            return $this->client;
        }
    }

    /**
     * Cluster-safe liveness check. PING has no key and therefore no hash slot,
     * so Predis refuses to route it in cluster mode — use a slot-routable
     * command against a sentinel key instead.
     */
    private function healthCheck(\Predis\Client $client): void {
        $client->exists('healthcheck');
    }

    /**
     * Creates and returns a new instance of the Predis client, configured with the
     * cache host and port retrieved from environment variables. Throws an exception
     * if either the host or port is not set.
     *
     * @return \Predis\Client The configured Predis client instance.
     * @throws \Exception If the cache host or port is not set.
     */
    private function createClient(): \Predis\Client {
        $host = getenv('CACHE_HOST') ?? '';
        $port = getenv('CACHE_PORT') ?? '';
        if (empty($host) || empty($port)) {
            throw new \Exception('Cache host or port not set');
        }
        return new \Predis\Client(
            [
                [
                    'scheme' => 'tcp',
                    'host' => $host,
                    'port' => $port,
                    'persistent' => true,
                    'timeout' => 1.0,
                    'read_write_timeout' => 1.0,
                ],
            ],
            [
                'cluster' => 'redis',
            ]
        );
    }
}

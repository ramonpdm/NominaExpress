<?php

namespace App\Config;

use Throwable;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\{Connection, DriverManager};
use Doctrine\ORM\{Configuration, EntityManager, ORMSetup};

use App\Exceptions\Database\DBConnectionException;

class ORM
{
    private Configuration $config;
    private Connection $conn;
    private EntityManager $em;
    private EventManager $evm;
    private array $driverOptions = [];

    public function __construct(?EntityManager $em = null)
    {
        if ($em) {
            $this->em = $em;
            return;
        }

        if ($this->hasDatabaseConfig()) {
            $this->initialize();
        }
    }

    private function hasDatabaseConfig(): bool
    {
        return isset($_ENV['DB_DRIVER'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_PORT']);
    }

    public function initialize(): void
    {
        $this->initializeConfig();
        $this->initializeConnection();
        $this->initializeEventManager();
        $this->initializeEntityManager();
    }

    private function initializeConfig(): void
    {
        $this->config = ORMSetup::createAttributeMetadataConfiguration([__DIR__ . '/../Entities'], $_ENV['ENVIRONMENT'] === 'development');
    }

    private function initializeConnection(): void
    {
        $this->conn = DriverManager::getConnection([
            'driver' => $_ENV['DB_DRIVER'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'dbname' => $_ENV['DB_NAME'],
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'driverOptions' => $this->driverOptions ?? [],
        ]);
    }

    private function initializeEventManager(): void
    {
        $this->evm = new EventManager();
    }

    private function initializeEntityManager(): void
    {
        $this->em = new EntityManager($this->conn, $this->config, $this->evm);
    }

    public function getProvider(): EntityManager
    {
        return $this->em;
    }

    public function getRepo(string $className): object
    {
        static $cache = [];

        if (isset($cache[$className])) {
            return $cache[$className];
        }

        return $cache[$className] = $this->getProvider()->getRepository($className);
    }

    public function isConnected(): bool
    {
        try {
            // Try to get the server version to check if the connection is valid
            return is_string($this->conn->getServerVersion());
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Determine if the database connection is valid.
     *
     * @throws DBConnectionException
     */
    public function valid(): ?bool
    {
        if (!isset($_ENV['DB_DRIVER'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'], $_ENV['DB_HOST'], $_ENV['DB_PORT'])) {
            throw new DBConnectionException('The database connection parameters are not set.');
        }

        if (!$this->isConnected()) {
            throw new DBConnectionException('The database connection could not be established with the server.');
        }

        return true;
    }

    /** @throws Throwable */
    public function beginTransaction(): void
    {
        $this->conn->beginTransaction();
    }

    /** @throws Throwable */
    public function commit(): void
    {
        if ($this->conn->isTransactionActive()) {
            $this->conn->commit();
        }
    }
}

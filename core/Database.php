<?php
// core/Database.php

namespace Core;

use PDO;
use PDOException;
use Exception;

class Database {
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Initialise la connexion à la base de données avec la configuration fournie.
     *
     * @param array $config Configuration de la base de données.
     * @return void
     * @throws Exception Si la connexion échoue.
     */
    public static function init(array $config): void {
        self::$config = $config;

        if (!self::$instance) {
            try {
                $dsn = sprintf(
                    '%s:host=%s;dbname=%s;port=%d;charset=%s',
                    $config['driver'],
                    $config['host'],
                    $config['database'],
                    $config['port'],
                    $config['charset'] ?? 'utf8'
                );

                self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => true // Utiliser des connexions persistantes pour de meilleures performances
                ]);
            } catch (PDOException $e) {
                throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
    }

    /**
     * Récupère l'instance PDO pour les opérations de base de données.
     *
     * @return PDO
     * @throws Exception Si l'instance n'est pas initialisée.
     */
    public static function getInstance(): PDO {
        if (!self::$instance) {
            throw new Exception("Base de données non initialisée. Appelez d'abord Database::init().");
        }
        return self::$instance;
    }

    /**
     * Exécute une requête SQL avec des paramètres facultatifs.
     *
     * @param string $query Requête SQL.
     * @param array $params Paramètres de la requête.
     * @return mixed Résultats de la requête.
     * @throws Exception Si une erreur survient lors de l'exécution de la requête.
     */
    public static function query(string $query, array $params = []): mixed {
        try {
            $stmt = self::getInstance()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'exécution de la requête : " . $e->getMessage());
        }
    }

    /**
     * Exécute une requête SQL de type modification (INSERT, UPDATE, DELETE).
     *
     * @param string $query Requête SQL.
     * @param array $params Paramètres de la requête.
     * @return int Nombre de lignes affectées.
     * @throws Exception Si une erreur survient lors de l'exécution de la requête.
     */
    public static function execute(string $query, array $params = []): int {
        try {
            $stmt = self::getInstance([])->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new \Exception("Erreur lors de l'exécution de la requête : " . $e->getMessage());
        }
    }

    /**
     * Commence une transaction.
     *
     * @return void
     * @throws Exception Si la transaction échoue.
     */
    public static function beginTransaction(): void {
        self::getInstance()->beginTransaction();
    }

    /**
     * Valide une transaction.
     *
     * @return void
     * @throws Exception Si la validation échoue.
     */
    public static function commit(): void {
        self::getInstance()->commit();
    }

    /**
     * Annule une transaction.
     *
     * @return void
     * @throws Exception Si l'annulation échoue.
     */
    public static function rollback(): void {
        self::getInstance()->rollBack();
    }

    /**
     * Ferme la connexion à la base de données.
     *
     * @return void
     */
    public static function close(): void {
        self::$instance = null;
    }
	
    public static function lastInsertId(): string {
        return self::getInstance([])->lastInsertId();
    }
}
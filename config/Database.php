<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Connexion PDO centralisee et securisee (singleton).
 * Configurable via variables d'environnement, avec valeurs par
 * defaut adaptees a une installation locale (XAMPP/WAMP/MAMP).
 */
class Database
{
    private static ?PDO $instance = null;

    private const HOST    = 'localhost';
    private const DBNAME  = 'fitconnect';
    private const USER    = 'root';
    private const PASS    = '';
    private const CHARSET = 'utf8mb4';

    private function __construct()
    {
        // Empeche l'instanciation directe
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host   = getenv('DB_HOST') ?: self::HOST;
            $dbname = getenv('DB_NAME') ?: self::DBNAME;
            $user   = getenv('DB_USER') ?: self::USER;
            $pass   = getenv('DB_PASS') ?: self::PASS;

            $dsn = "mysql:host={$host};dbname={$dbname};charset=" . self::CHARSET;

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // On ne divulgue jamais les identifiants dans le message d'erreur
                throw new PDOException('Connexion a la base de donnees impossible : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}

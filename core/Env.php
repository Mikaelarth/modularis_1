<?php
// core/Env.php

namespace Core;

use Exception;

class Env {
    protected static array $variables = [];

    /**
     * Charge le fichier .env et initialise les variables d'environnement.
     *
     * @param string $filePath Le chemin vers le fichier .env.
     * @return void
     * @throws Exception Si le fichier .env est introuvable ou si une clé est invalide.
     */
    public static function load(string $filePath): void {
        if (!file_exists($filePath)) {
            throw new Exception("Le fichier .env est introuvable : $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (self::isComment($line)) {
                continue;
            }

            [$key, $value] = self::parseEnvLine($line);

            // Valider la clé
            if (!self::isValidKey($key)) {
                throw new Exception("Clé d'environnement invalide détectée : $key");
            }

            self::$variables[$key] = self::parseValue($value);
        }
    }

    /**
     * Vérifie si une ligne est un commentaire.
     *
     * @param string $line La ligne à vérifier.
     * @return bool Vrai si la ligne est un commentaire, faux sinon.
     */
    protected static function isComment(string $line): bool {
        return strpos(trim($line), '#') === 0;
    }

    /**
     * Analyse une ligne de variable d'environnement.
     *
     * @param string $line La ligne à analyser.
     * @return array Tableau contenant la clé et la valeur.
     * @throws Exception Si la ligne n'est pas correctement formatée.
     */
    protected static function parseEnvLine(string $line): array {
        if (strpos($line, '=') === false) {
            throw new Exception("Ligne de configuration invalide : $line");
        }

        [$key, $value] = explode('=', $line, 2);
        return [trim($key), trim($value)];
    }

	
    /**
     * Valide une clé d'environnement.
     *
     * @param string $key La clé à valider.
     * @return bool Vrai si la clé est valide, faux sinon.
     */
    protected static function isValidKey(string $key): bool {
        return preg_match('/^[A-Z0-9_]+$/', $key);
    }

    public static function validateKey(string $key): bool {
        return self::isValidKey($key);
    }

    /**
     * Analyse et convertit une valeur d'environnement.
     *
     * @param string $value La valeur à analyser.
     * @return mixed La valeur convertie.
     */
    protected static function parseValue(string $value): mixed {
        $value = trim($value);

        // Convertir les valeurs booléennes
        if (strtolower($value) === 'true' || strtolower($value) === '(true)') {
            return true;
        }
        if (strtolower($value) === 'false' || strtolower($value) === '(false)') {
            return false;
        }

        // Convertir les valeurs nulles
        if (strtolower($value) === 'null' || strtolower($value) === '(null)') {
            return null;
        }

        // Convertir les valeurs numériques
        if (is_numeric($value)) {
            return $value + 0;
        }

        // Retirer les guillemets autour des chaînes
        return trim($value, '"\'');
    }

    /**
     * Récupère la valeur d'une variable d'environnement.
     *
     * @param string $key La clé de la variable d'environnement.
     * @param mixed $default Valeur par défaut si la clé n'est pas trouvée.
     * @return mixed La valeur de la variable d'environnement ou la valeur par défaut.
     */
    public static function get(string $key, mixed $default = null): mixed {
        return self::$variables[$key] ?? $default;
    }

    /**
     * Définit une variable d'environnement.
     *
     * @param string $key La clé de la variable.
     * @param mixed $value La valeur à définir.
     * @return void
     */
     public static function set(string $key, mixed $value): void {
        // Vérifie que la clé est valide avant de la définir
        if (!self::isValidKey($key)) {
            // On peut décider de lever une exception ou simplement ignorer le réglage
            return; // Ignore si la clé n'est pas valide
        }

        self::$variables[$key] = $value;
    }

    /**
     * Retourne toutes les variables d'environnement chargées.
     *
     * @return array Les variables d'environnement.
     */
    public static function all(): array {
        return self::$variables;
    }

    /**
     * Réinitialise toutes les variables d'environnement chargées.
     *
     * @return void
     */
    public static function reset(): void {
        self::$variables = [];
    }
}

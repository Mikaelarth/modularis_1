<?php

namespace Core;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Logger {
    private static ?MonoLogger $instance = null;
    private static string $logFile = __DIR__ . '/../logs/app.log'; // Chemin par défaut
    private static int $logLevel = MonoLogger::DEBUG; // Niveau de log par défaut

    /**
     * Initialiser le logger avec des paramètres de configuration optionnels.
     *
     * @param string $logFile Chemin du fichier de log.
     * @param int $logLevel Niveau de log.
     * @return void
     */
	public static function init(string $logFile = null, int $logLevel = null): void {
		if ($logFile !== null) {
			self::$logFile = $logFile;
		} else {
			// Fichier de log par défaut
			self::$logFile = __DIR__ . '/../logs/app.log';
		}

		if ($logLevel !== null) {
			self::$logLevel = $logLevel;
		} else {
			// Niveau de log par défaut
			self::$logLevel = MonoLogger::DEBUG;
		}

		// Réinitialiser l'instance du logger avec les nouveaux paramètres
		self::$instance = new MonoLogger('modularis');
		self::$instance->pushHandler(new StreamHandler(self::$logFile, self::$logLevel));
	}


    /**
     * Obtenir l'instance du logger.
     *
     * @return MonoLogger
     */
    public static function getInstance(): MonoLogger {
        if (self::$instance === null) {
            self::$instance = new MonoLogger('modularis');

            $handler = new StreamHandler(self::$logFile, self::$logLevel);
            $formatter = new LineFormatter(null, null, false, true);
            $handler->setFormatter($formatter);

            self::$instance->pushHandler($handler);
        }

        return self::$instance;
    }

    /**
     * Enregistrer un message d'information.
     *
     * @param string $message Message à enregistrer
     * @param array $context Contexte additionnel
     * @return void
     */
    public static function info(string $message, array $context = []): void {
        self::getInstance()->info($message, $context);
    }

    /**
     * Enregistrer un message d'erreur.
     *
     * @param string $message Message à enregistrer
     * @param array $context Contexte additionnel
     * @return void
     */
    public static function error(string $message, array $context = []): void {
        self::getInstance()->error($message, $context);
    }

    /**
     * Enregistrer un message d'avertissement.
     *
     * @param string $message Message à enregistrer
     * @param array $context Contexte additionnel
     * @return void
     */
    public static function warning(string $message, array $context = []): void {
        self::getInstance()->warning($message, $context);
    }

    /**
     * Enregistrer un message de débogage.
     *
     * @param string $message Message à enregistrer
     * @param array $context Contexte additionnel
     * @return void
     */
    public static function debug(string $message, array $context = []): void {
        self::getInstance()->debug($message, $context);
    }

    /**
     * Effacer les logs.
     *
     * @return void
     */
    public static function clear(): void {
        file_put_contents(self::$logFile, '');
    }
}

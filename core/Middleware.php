<?php

namespace Core;

abstract class Middleware
{
    // Déclarez la propriété comme callable avec une valeur par défaut
    protected static $headerSender = 'header';

    /**
     * Méthode à implémenter par chaque middleware spécifique.
     */
    abstract public static function handle();

    /**
     * Redirige vers une autre URL.
     *
     * @param string $url URL de destination.
     */
	public static function redirect(string $url): void
	{
		$headerSender = self::$headerSender;
		$headerSender('Location: ' . $url);

		// Utilisation d'une exception au lieu de 'exit' pour les tests
		if (defined('TESTING_ENV')) {
			throw new \Exception('Exit called after redirect');
		} else {
			exit;
		}
	}


    /**
     * Retourne une réponse JSON.
     *
     * @param array $data Données à retourner en JSON.
     */
	public static function jsonResponse(array $data): void
	{
		$headerSender = self::$headerSender;
		$headerSender('Content-Type: application/json');
		echo json_encode($data);

		// Utilisation d'une exception pour les tests au lieu de 'exit'
		if (defined('TESTING_ENV')) {
			throw new \Exception('Exit called after jsonResponse');
		} else {
			exit;
		}
	}


    /**
     * Définit un expéditeur d'en-tête personnalisé (pour les tests).
     *
     * @param callable $sender Fonction d'envoi d'en-tête.
     */
    public static function setHeaderSender(callable $sender): void
    {
        if (is_callable($sender)) {
            self::$headerSender = $sender;
        } else {
            throw new \InvalidArgumentException('Sender must be callable.');
        }
    }
}

// Initialisez la valeur par défaut pour $headerSender comme la fonction 'header'
Middleware::setHeaderSender('header');

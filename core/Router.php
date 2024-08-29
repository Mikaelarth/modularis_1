<?php

namespace Core;

use Exception;

class Router
{
    private static array $routes = [];
    private static string $basePath = '';
    protected static $headerSender = 'header';

    /**
     * Définit le chemin de base pour toutes les routes.
     *
     * @param string $basePath Chemin de base pour les routes.
     */
    public static function setBasePath(string $basePath): void
    {
        self::$basePath = rtrim($basePath, '/');
    }

    /**
     * Réinitialise toutes les routes.
     */
    public static function resetRoutes(): void
    {
        self::$routes = [];
    }

    /**
     * Ajoute une route au routeur.
     *
     * @param string $method Méthode HTTP (GET, POST, PUT, DELETE, etc.).
     * @param string $route Chemin de la route.
     * @param callable $handler Fonction de rappel à exécuter pour cette route.
     * @param array $middlewares Middlewares à appliquer à cette route.
     */
    public static function add(string $method, string $route, callable $handler, array $middlewares = []): void
    {
        $method = strtoupper($method);
        $route = self::$basePath . '/' . trim($route, '/');
        
        self::$routes[$method][] = [
            'route' => $route,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Ajoute une route GET.
     */
    public static function get(string $route, callable $handler, array $middlewares = []): void
    {
        self::add('GET', $route, $handler, $middlewares);
    }

    /**
     * Ajoute une route POST.
     */
    public static function post(string $route, callable $handler, array $middlewares = []): void
    {
        self::add('POST', $route, $handler, $middlewares);
    }

    /**
     * Ajoute une route PUT.
     */
    public static function put(string $route, callable $handler, array $middlewares = []): void
    {
        self::add('PUT', $route, $handler, $middlewares);
    }

    /**
     * Ajoute une route DELETE.
     */
    public static function delete(string $route, callable $handler, array $middlewares = []): void
    {
        self::add('DELETE', $route, $handler, $middlewares);
    }

    /**
     * Gère la requête en fonction de l'URI et de la méthode.
     *
     * @param string $requestUri URI de la requête.
     * @param string $requestMethod Méthode HTTP de la requête.
     */
    public static function dispatch(string $requestUri, string $requestMethod): void
    {
        $method = strtoupper($requestMethod);
        if (!isset(self::$routes[$method])) {
            self::sendNotFoundResponse();
            return;
        }

        foreach (self::$routes[$method] as $route) {
            $regex = self::convertRouteToRegex($route['route']);
            if (preg_match($regex, $requestUri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Exécuter les middlewares
                self::executeMiddlewares($route['middlewares']);

                // Appeler le gestionnaire de route
                call_user_func_array($route['handler'], $params);
                return;
            }
        }

        self::sendNotFoundResponse();
    }

    /**
     * Convertit une route en expression régulière pour la correspondance.
     *
     * @param string $route Route à convertir.
     * @return string Expression régulière.
     */
    private static function convertRouteToRegex(string $route): string
    {
        // Convertir les segments dynamiques en regex
        $route = preg_replace('/{([^\/]+)}/', '(?P<\1>[^\/]+)', $route);
        return "@^" . $route . "$@D";
    }

    /**
     * Exécute les middlewares pour une route donnée.
     *
     * @param array $middlewares Liste des middlewares à exécuter.
     */
    protected static function executeMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                $result = $middleware();
            } elseif (is_string($middleware) && class_exists($middleware)) {
                $result = (new $middleware)->handle();
            } else {
                continue; // Ignore les middlewares non valides
            }

            if ($result === false) {
                // Stopper l'exécution si un middleware retourne false
                return;
            }
        }
    }

    /**
     * Redirige vers une autre URL.
     *
     * @param string $url URL de destination.
     */
    public static function redirect(string $url): void
    {
        $headerSender = self::$headerSender;
        $headerSender('Location: ' . $url);
        exit;
    }

    /**
     * Retourne une réponse JSON.
     *
     * @param array $data Données à retourner en JSON.
     */
    public static function json(array $data): void
    {
        $headerSender = self::$headerSender;
        $headerSender('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Définit un expéditeur d'en-tête personnalisé (pour les tests).
     *
     * @param callable $sender Fonction d'envoi d'en-tête.
     */
    public static function setHeaderSender(callable $sender): void
    {
        self::$headerSender = $sender;
    }

    /**
     * Envoie une réponse 404 Not Found.
     */
    private static function sendNotFoundResponse(): void
    {
        http_response_code(404);
        echo '404 - Page Not Found';
    }

    /**
     * Permet de définir des groupes de routes avec un préfixe commun.
     *
     * @param string $prefix Préfixe du groupe de routes.
     * @param callable $callback Fonction de rappel contenant les définitions de route.
     * @param array $middlewares Middlewares à appliquer à toutes les routes du groupe.
     */
    public static function group(string $prefix, callable $callback, array $middlewares = []): void
    {
        $currentBasePath = self::$basePath;
        self::$basePath .= '/' . trim($prefix, '/');
        $callback();
        self::$basePath = $currentBasePath; // Réinitialiser le chemin de base après le groupe
    }
}

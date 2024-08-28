<?php

namespace Core;

class Router
{
    private static array $routes = [];
    private static string $basePath = '';

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
     * Ajoute une route au routeur.
     *
     * @param string $method Méthode HTTP (GET, POST, PUT, DELETE, etc.).
     * @param string $route Chemin de la route.
     * @param callable $handler Fonction de rappel à exécuter pour cette route.
     * @param array $middlewares Middlewares à appliquer à cette route.
     */
    public static function add(string $method, string $route, callable $handler, array $middlewares = []): void
    {
        self::$routes[] = [
            'method' => strtoupper($method),
            'route' => self::$basePath . '/' . trim($route, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares,
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
        foreach (self::$routes as $route) {
            if ($route['method'] === strtoupper($requestMethod) && preg_match(self::convertRouteToRegex($route['route']), $requestUri, $matches)) {
                array_shift($matches); // Enlever la correspondance complète
                foreach ($route['middlewares'] as $middleware) {
                    $middleware::handle();
                }
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page Not Found';
    }

    /**
     * Convertit une route en expression régulière pour la correspondance.
     *
     * @param string $route Route à convertir.
     * @return string Expression régulière.
     */
    private static function convertRouteToRegex(string $route): string
    {
        $route = preg_replace('/{([^\/]+)}/', '([^\/]+)', $route);
        return "@^" . $route . "$@D";
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

    /**
     * Redirige vers une autre URL.
     *
     * @param string $url URL de destination.
     */
    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Retourne une réponse JSON.
     *
     * @param array $data Données à retourner en JSON.
     */
    public static function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
	
	
    protected static $headerSender = 'header';

    public static function setHeaderSender(callable $sender)
    {
        self::$headerSender = $sender;
    }	
}

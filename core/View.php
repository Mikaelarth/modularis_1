<?php

namespace Core;

class View {
    // Chemins par défaut pour les vues et les layouts
    protected static string $viewsPath = __DIR__ . '/../modules/';
    protected static string $layoutsPath = __DIR__ . '/../modules/';

    /**
     * Rendre un fichier de vue avec un layout.
     *
     * @param string $view Le nom du fichier de vue à rendre.
     * @param array $data Les données à passer à la vue.
     * @param string|null $layout Le nom du layout à utiliser (optionnel).
     * @return void
     * @throws \Exception Si le fichier de vue ou le layout n'existe pas.
     */
    public static function render(string $view, array $data = [], string $layout = null): void {
        // Convertir le chemin de la vue en chemin absolu
        $viewPath = self::$viewsPath . $view . '.php';

        // Log du chemin de la vue utilisée pour déboguer
        Logger::getInstance(Core::getConfig('log'))->info("Chemin de la vue utilisé : " . realpath($viewPath));

        // Vérifier l'existence du fichier de vue
        if (!file_exists($viewPath)) {
            throw new \Exception("La vue {$view} n'existe pas. Chemin utilisé : {$viewPath}");
        }

        // Extraire les données pour les rendre disponibles dans la vue
        extract(self::escapeData($data));

        // Tampon de sortie pour capturer le rendu de la vue
        ob_start();

        // Inclure le layout s'il est spécifié, sinon rendre la vue directement
        if ($layout) {
            $layoutPath = self::$layoutsPath . $layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new \Exception("Le layout {$layout} n'existe pas. Chemin utilisé : {$layoutPath}");
            }
            include $layoutPath;
        } else {
            include $viewPath;
        }

        echo ob_get_clean(); // Envoyer le contenu tamponné au client
    }

    /**
     * Inclure un fichier de vue partielle.
     *
     * @param string $partial Le nom de la vue partielle à inclure.
     * @param array $data Les données à passer à la vue partielle.
     * @return void
     * @throws \Exception Si le fichier de vue partielle n'existe pas.
     */
    public static function renderPartial(string $partial, array $data = []): void {
        $partialPath = self::$viewsPath . $partial . '.php';

        // Vérifier l'existence du fichier de vue partielle
        if (!file_exists($partialPath)) {
            throw new \Exception("La vue partielle {$partial} n'existe pas. Chemin utilisé : {$partialPath}");
        }

        // Extraire les données pour les rendre disponibles dans la vue partielle
        extract(self::escapeData($data));

        // Tampon de sortie pour capturer le rendu de la vue partielle
        ob_start();
        include $partialPath;
        echo ob_get_clean(); // Envoyer le contenu tamponné au client
    }

    /**
     * Définir un chemin personnalisé pour les vues.
     *
     * @param string $path Le nouveau chemin pour les vues.
     * @return void
     */
    public static function setViewsPath(string $path): void {
        self::$viewsPath = rtrim($path, '/') . '/';
    }

    /**
     * Définir un chemin personnalisé pour les layouts.
     *
     * @param string $path Le nouveau chemin pour les layouts.
     * @return void
     */
    public static function setLayoutsPath(string $path): void {
        self::$layoutsPath = rtrim($path, '/') . '/';
    }

    /**
     * Échapper les données pour la sécurité.
     *
     * @param array $data Les données à échapper.
     * @return array Les données échappées.
     */
    private static function escapeData(array $data): array {
        return array_map(function ($value) {
            return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        }, $data);
    }
}

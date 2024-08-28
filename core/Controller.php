<?php

namespace Core;

class Controller {
    /**
     * Méthode utilitaire pour rediriger vers une autre URL.
     *
     * @param string $url L'URL vers laquelle rediriger
     * @return void
     */
    protected function redirect(string $url): void {
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Rendre une vue avec un layout.
     *
     * @param string $view Nom de la vue
     * @param array $data Données à passer à la vue
     * @param string|null $layout Nom du layout (optionnel)
     * @return void
     */
    protected function render(string $view, array $data = [], string $layout = null): void {
        View::render($view, $data, $layout);
    }
}

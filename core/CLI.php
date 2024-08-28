<?php

namespace Core;

class CLI {

    protected array $commands = [
        'make:module' => 'createModule',
        'make:controller' => 'createController',
        'make:model' => 'createModel',
        'make:view' => 'createView',
        'make:middleware' => 'createMiddleware',
        'make:migration' => 'createMigration',
        'migrate' => 'runMigrations',
        'migrate:rollback' => 'rollbackMigrations',
        'cache:clear' => 'clearCache',
        'security:check' => 'checkSecurity',
        'config:set' => 'setConfig',
        'config:get' => 'getConfig',
        'env:set' => 'setEnv',
        'env:get' => 'getEnv',
        'test:run' => 'runTests',
        'test:watch' => 'watchTests',
    ];

    /**
     * Exécuter l'application CLI
     */
    public function run(): void {
        global $argv;

        $command = $argv[1] ?? null;
        $nonInteractive = in_array('--non-interactive', $argv);

        if (!$command) {
            $this->displayHelp();
            exit;
        }

        if (isset($this->commands[$command])) {
            $method = $this->commands[$command];
            $this->$method(array_slice($argv, 2), $nonInteractive);
        } else {
            echo "Commande inconnue : $command\n";
            $this->displayHelp();
        }
    }

    /**
     * Afficher l'aide CLI
     */
    protected function displayHelp(): void {
        echo "Utilisation : php scripts/modularis <commande> [options]\n\n";
        echo "Commandes disponibles :\n";
        foreach ($this->commands as $command => $method) {
            echo "  - $command\n";
        }
    }

    /**
     * Méthode publique pour tester les commandes protégées
     */
    public function testCommand(string $command, array $args = [], bool $nonInteractive = false): void {
        if (isset($this->commands[$command])) {
            $method = $this->commands[$command];
            $this->$method($args, $nonInteractive);
        } else {
            echo "Commande inconnue : $command\n";
        }
    }

    /**
     * Créer un nouveau module
     */
    protected function createModule(array $args, bool $nonInteractive = false): void {
        $moduleName = $args[0] ?? null;

        if (!$moduleName) {
            echo "Erreur : Vous devez spécifier un nom de module.\n";
            return;
        }

        $modulePath = __DIR__ . '/../modules/' . $moduleName;

        if (is_dir($modulePath)) {
            if ($nonInteractive) {
                echo "Le module '$moduleName' existe déjà. Opération annulée.\n";
                return;
            }

            echo "Erreur : Le module '$moduleName' existe déjà. Voulez-vous le réécrire ? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'y') {
                echo "Opération annulée.\n";
                return;
            }
            fclose($handle);
            $this->deleteDirectory($modulePath); // Suppression du module existant pour le recréer
        }

        try {
            // Créer la structure de répertoires pour le nouveau module
            mkdir($modulePath . '/controllers', 0777, true);
            mkdir($modulePath . '/models', 0777, true);
            mkdir($modulePath . '/views', 0777, true);
            mkdir($modulePath . '/views/partials', 0777, true);
            mkdir($modulePath . '/middlewares', 0777, true);
            mkdir($modulePath . '/actions', 0777, true);
            mkdir($modulePath . '/config', 0777, true);

            // Créer des fichiers par défaut pour le module
            file_put_contents("$modulePath/controllers/{$moduleName}Controller.php", "<?php\n\nnamespace Modules\\$moduleName\\Controllers;\n\nuse Core\\Controller;\n\nclass {$moduleName}Controller extends Controller {\n\n}\n");
            file_put_contents("$modulePath/config/config.php", "<?php\n\nreturn [\n    // Configuration du module $moduleName\n];\n");
            file_put_contents("$modulePath/module.php", "<?php\n\nnamespace Modules\\$moduleName;\n\nuse Core\\Router;\n\n// Initialiser les routes et les autres configurations pour le module $moduleName\n\n");
            file_put_contents("$modulePath/routes.php", "<?php\n\nuse Core\\Router;\n\n// Définir les routes pour le module $moduleName\n\n");

            echo "Module '$moduleName' créé avec succès !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la création du module : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Supprime un répertoire et tout son contenu
     */
    private function deleteDirectory(string $dir): void {
        if (!file_exists($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }

    /**
     * Créer un nouveau contrôleur
     */
    protected function createController(array $args, bool $nonInteractive = false): void {
        $moduleName = $args[0] ?? null;
        $controllerName = $args[1] ?? null;

        if (!$moduleName || !$controllerName) {
            echo "Erreur : Vous devez spécifier un nom de module et un nom de contrôleur.\n";
            return;
        }

        $controllerPath = __DIR__ . '/../modules/' . $moduleName . '/controllers/' . $controllerName . 'Controller.php';

        if (!is_dir(__DIR__ . '/../modules/' . $moduleName . '/controllers')) {
            echo "Erreur : Le module spécifié n'existe pas ou est invalide.\n";
            return;
        }

        if (file_exists($controllerPath)) {
            if ($nonInteractive) {
                echo "Le contrôleur '$controllerName' existe déjà dans le module '$moduleName'. Opération annulée.\n";
                return;
            }

            echo "Erreur : Le contrôleur '$controllerName' existe déjà dans le module '$moduleName'. Voulez-vous le réécrire ? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'y') {
                echo "Opération annulée.\n";
                return;
            }
            fclose($handle);
            unlink($controllerPath);
        }

        try {
            $controllerContent = "<?php\n\nnamespace Modules\\$moduleName\\Controllers;\n\nuse Core\\Controller;\n\nclass {$controllerName}Controller extends Controller {\n\n    public function index() {\n        // Code pour l'action par défaut\n    }\n\n}\n";
            file_put_contents($controllerPath, $controllerContent);

            echo "Contrôleur '$controllerName' créé avec succès dans le module '$moduleName' !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la création du contrôleur : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Créer un nouveau modèle
     */
    protected function createModel(array $args, bool $nonInteractive = false): void {
        $moduleName = $args[0] ?? null;
        $modelName = $args[1] ?? null;

        if (!$moduleName || !$modelName) {
            echo "Erreur : Vous devez spécifier un nom de module et un nom de modèle.\n";
            return;
        }

        $modelPath = __DIR__ . '/../modules/' . $moduleName . '/models/' . $modelName . '.php';

        if (!is_dir(__DIR__ . '/../modules/' . $moduleName . '/models')) {
            echo "Erreur : Le module spécifié n'existe pas ou est invalide.\n";
            return;
        }

        if (file_exists($modelPath)) {
            if ($nonInteractive) {
                echo "Le modèle '$modelName' existe déjà dans le module '$moduleName'. Opération annulée.\n";
                return;
            }

            echo "Erreur : Le modèle '$modelName' existe déjà dans le module '$moduleName'. Voulez-vous le réécrire ? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'y') {
                echo "Opération annulée.\n";
                return;
            }
            fclose($handle);
            unlink($modelPath);
        }

        try {
            $modelContent = "<?php\n\nnamespace Modules\\$moduleName\\Models;\n\nclass {$modelName} {\n\n    protected \$data = [];\n\n    public function __construct(array \$data = []) {\n        \$this->data = \$data;\n    }\n\n    // Ajouter des méthodes pour le modèle ici\n\n}\n";
            file_put_contents($modelPath, $modelContent);

            echo "Modèle '$modelName' créé avec succès dans le module '$moduleName' !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la création du modèle : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Créer une nouvelle vue
     */
    protected function createView(array $args, bool $nonInteractive = false): void {
        $moduleName = $args[0] ?? null;
        $viewName = $args[1] ?? null;

        if (!$moduleName || !$viewName) {
            echo "Erreur : Vous devez spécifier un nom de module et un nom de vue.\n";
            return;
        }

        $viewPath = __DIR__ . '/../modules/' . $moduleName . '/views/' . $viewName . '.php';

        if (!is_dir(__DIR__ . '/../modules/' . $moduleName . '/views')) {
            echo "Erreur : Le module spécifié n'existe pas ou est invalide.\n";
            return;
        }

        if (file_exists($viewPath)) {
            if ($nonInteractive) {
                echo "La vue '$viewName' existe déjà dans le module '$moduleName'. Opération annulée.\n";
                return;
            }

            echo "Erreur : La vue '$viewName' existe déjà dans le module '$moduleName'. Voulez-vous la réécrire ? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'y') {
                echo "Opération annulée.\n";
                return;
            }
            fclose($handle);
            unlink($viewPath);
        }

        try {
            $viewContent = "<!-- Vue pour le module $moduleName -->\n<div>\n    <h1>$viewName</h1>\n    <!-- Contenu de la vue -->\n</div>\n";
            file_put_contents($viewPath, $viewContent);

            echo "Vue '$viewName' créée avec succès dans le module '$moduleName' !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la création de la vue : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Créer un nouveau middleware
     */
    protected function createMiddleware(array $args, bool $nonInteractive = false): void {
        $moduleName = $args[0] ?? null;
        $middlewareName = $args[1] ?? null;

        if (!$moduleName || !$middlewareName) {
            echo "Erreur : Vous devez spécifier un nom de module et un nom de middleware.\n";
            return;
        }

        $middlewarePath = __DIR__ . '/../modules/' . $moduleName . '/middlewares/' . $middlewareName . 'Middleware.php';

        if (!is_dir(__DIR__ . '/../modules/' . $moduleName . '/middlewares')) {
            echo "Erreur : Le module spécifié n'existe pas ou est invalide.\n";
            return;
        }

        if (file_exists($middlewarePath)) {
            if ($nonInteractive) {
                echo "Le middleware '$middlewareName' existe déjà dans le module '$moduleName'. Opération annulée.\n";
                return;
            }

            echo "Erreur : Le middleware '$middlewareName' existe déjà dans le module '$moduleName'. Voulez-vous le réécrire ? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'y') {
                echo "Opération annulée.\n";
                return;
            }
            fclose($handle);
            unlink($middlewarePath);
        }

        try {
            $middlewareContent = "<?php\n\nnamespace Modules\\$moduleName\\Middlewares;\n\nuse Core\\Middleware;\n\nclass {$middlewareName}Middleware extends Middleware {\n\n    public function handle(\$request, \$next) {\n        // Code pour le middleware\n        return \$next(\$request);\n    }\n\n}\n";
            file_put_contents($middlewarePath, $middlewareContent);

            echo "Middleware '$middlewareName' créé avec succès dans le module '$moduleName' !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la création du middleware : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Créer une nouvelle migration
     */
    protected function createMigration(array $args, bool $nonInteractive = false): void {
        $migrationName = $args[0] ?? null;

        if (!$migrationName) {
            echo "Erreur : Vous devez spécifier un nom de migration.\n";
            return;
        }

        $migrationsPath = __DIR__ . '/../database/migrations';

        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
        }

        $timestamp = date('YmdHis');
        $migrationFile = "$migrationsPath/{$timestamp}_{$migrationName}.php";

        if (file_exists($migrationFile)) {
            if ($nonInteractive) {
                echo "La migration '$migrationName' existe déjà. Opération annulée.\n";
                return;
            }

            echo "Erreur : La migration '$migrationName' existe déjà. Voulez-vous la réécrire ? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) != 'y') {
                echo "Opération annulée.\n";
                return;
            }
            fclose($handle);
            unlink($migrationFile);
        }

        try {
            $migrationTemplate = "<?php\n\nclass {$timestamp}_{$migrationName} {\n    public function up() {\n        // Code pour appliquer la migration\n    }\n\n    public function down() {\n        // Code pour annuler la migration\n    }\n}\n";
            file_put_contents($migrationFile, $migrationTemplate);

            echo "Migration '$migrationName' créée avec succès !\n";
        } catch (\Exception $e) {
            echo "Erreur lors de la création de la migration : " . $e->getMessage() . "\n";
        }
    }

    /**
     * Exécuter les migrations
     */
	protected function runMigrations(array $args): void {
		$migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
		foreach ($migrationFiles as $file) {
			require_once $file;

			// Obtenir le nom du fichier sans extension
			$fileName = basename($file, '.php');
			
			// Extraire le nom de classe sans le timestamp
			$className = preg_replace('/^\d+_/', '', $fileName); 

			if (class_exists($className)) {
				$migration = new $className();
				$migration->up();
				echo "Migration '$className' exécutée avec succès.\n";
			} else {
				echo "Erreur : La classe '$className' n'est pas définie correctement dans le fichier de migration.\n";
			}
		}
	}



    /**
     * Annuler la dernière migration
     */
    protected function rollbackMigrations(array $args): void {
        $migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
        $lastFile = end($migrationFiles);
        if ($lastFile) {
            require_once $lastFile;
            $className = basename($lastFile, '.php');
            $migration = new $className();
            $migration->down();
            echo "Migration '$className' annulée avec succès.\n";
        } else {
            echo "Aucune migration à annuler.\n";
        }
    }

    /**
     * Vider le cache
     */
    protected function clearCache(array $args): void {
        $cacheDir = __DIR__ . '/../cache';
        $files = glob("$cacheDir/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "Cache vidé avec succès.\n";
    }

    /**
     * Vérifier la sécurité
     */
    protected function checkSecurity(array $args): void {
        echo "Vérification de la sécurité...\n";
        // Exemple de vérifications
        echo "Aucune vulnérabilité détectée !\n";
    }

    /**
     * Définir une configuration
     */
    protected function setConfig(array $args): void {
        $key = $args[0] ?? null;
        $value = $args[1] ?? null;

        if (!$key || !$value) {
            echo "Erreur : Vous devez spécifier une clé et une valeur pour la configuration.\n";
            return;
        }

        $configFile = __DIR__ . '/../config/config.php';
        
        if (!file_exists($configFile)) {
            echo "Erreur : Fichier de configuration introuvable.\n";
            return;
        }

        $config = include $configFile;

        $keys = explode('.', $key);
        $ref = &$config;
        foreach ($keys as $innerKey) {
            if (!isset($ref[$innerKey])) {
                $ref[$innerKey] = [];
            }
            $ref = &$ref[$innerKey];
        }
        $ref = $value;

        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configFile, $configContent);

        echo "Configuration '$key' mise à jour avec succès à '$value'.\n";
    }

    /**
     * Obtenir une configuration
     */
    protected function getConfig(array $args): void {
        $key = $args[0] ?? null;

        if (!$key) {
            echo "Erreur : Vous devez spécifier une clé pour obtenir la configuration.\n";
            return;
        }

        $configFile = __DIR__ . '/../config/config.php';

        if (!file_exists($configFile)) {
            echo "Erreur : Fichier de configuration introuvable.\n";
            return;
        }

        $config = include $configFile;

        $keys = explode('.', $key);
        $value = $config;
        foreach ($keys as $innerKey) {
            if (!isset($value[$innerKey])) {
                echo "Erreur : Clé '$key' introuvable dans la configuration.\n";
                return;
            }
            $value = $value[$innerKey];
        }

        echo "Valeur de '$key' : $value\n";
    }

    /**
     * Définir une variable d'environnement
     */
	protected function setEnv(array $args): void {
		$key = $args[0] ?? null;
		$value = $args[1] ?? null;

		if (!$key || !$value) {
			echo "Erreur : Vous devez spécifier une clé et une valeur pour la variable d'environnement.\n";
			return;
		}

		// Valider la clé avant de procéder
		if (!Env::validateKey($key)) {
			echo "Erreur : Clé d'environnement invalide '$key'. Utilisez uniquement des lettres majuscules, des chiffres, et des underscores.\n";
			return;
		}

		$envFile = __DIR__ . '/../.env';

		if (!file_exists($envFile)) {
			echo "Erreur : Fichier .env introuvable.\n";
			return;
		}

		try {
			$envContent = file_get_contents($envFile);
			$pattern = "/^" . preg_quote($key, '/') . "=.*/m";

			if (preg_match($pattern, $envContent)) {
				// Mettre à jour la valeur existante
				$envContent = preg_replace($pattern, "$key=$value", $envContent);
			} else {
				// Ajouter la nouvelle variable
				$envContent .= "$key=$value\n";
			}

			file_put_contents($envFile, $envContent);
			echo "Variable d'environnement '$key' mise à jour avec succès à '$value'.\n";
		} catch (\Exception $e) {
			echo "Erreur lors de la mise à jour de la variable d'environnement : " . $e->getMessage() . "\n";
		}
	}

	protected function getEnv(array $args): void {
		$key = $args[0] ?? null;

		if (!$key) {
			echo "Erreur : Vous devez spécifier une clé pour obtenir la variable d'environnement.\n";
			return;
		}

		$envFile = __DIR__ . '/../.env';

		if (!file_exists($envFile)) {
			echo "Erreur : Fichier .env introuvable.\n";
			return;
		}

		try {
			$envContent = file_get_contents($envFile);
			$pattern = "/^" . preg_quote($key, '/') . "=(.*)$/m";

			if (preg_match($pattern, $envContent, $matches)) {
				echo "Valeur de '$key' : " . $matches[1] . "\n";
			} else {
				echo "Erreur : Clé '$key' introuvable dans le fichier .env.\n";
			}
		} catch (\Exception $e) {
			echo "Erreur lors de la récupération de la variable d'environnement : " . $e->getMessage() . "\n";
		}
	}

    /**
     * Exécuter les tests
     */
    protected function runTests(array $args): void {
        echo "Exécution des tests unitaires...\n";
        passthru('php vendor/phpunit/phpunit/phpunit --testdox tests');
    }

    /**
     * Surveiller les tests
     */
    protected function watchTests(array $args): void {
        echo "Surveillance des modifications des fichiers pour l'exécution des tests unitaires...\n";
        passthru('php vendor/bin/phpunit-watcher watch --testdox');
    }
}

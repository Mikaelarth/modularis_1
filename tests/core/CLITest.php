<?php

use PHPUnit\Framework\TestCase;
use Core\CLI;

class CLITest extends TestCase {

    public function testCreateModule(): void {
        $cli = new CLI();

        $moduleName = 'testmodule';
        $cli->testCommand('make:module', [$moduleName]);

        $this->assertDirectoryExists(__DIR__ . "/../../modules/$moduleName");

        $this->cleanUpModule($moduleName);
    }

    public function testCreateController(): void {
        $cli = new CLI();
        $moduleName = 'testmodule';
        $controllerName = 'TestController';

        if (!file_exists(__DIR__ . "/../../modules/$moduleName")) {
            mkdir(__DIR__ . "/../../modules/$moduleName", 0777, true);
        }
        if (!file_exists(__DIR__ . "/../../modules/$moduleName/controllers")) {
            mkdir(__DIR__ . "/../../modules/$moduleName/controllers", 0777, true);
        }

        $cli->testCommand('make:controller', [$moduleName, $controllerName]);

        $this->assertFileExists(__DIR__ . "/../../modules/$moduleName/controllers/{$controllerName}Controller.php");

        $this->cleanUpModule($moduleName);
    }

    // Test pour créer une migration
	public function testCreateMigration(): void {
		$cli = new CLI();
		$migrationName = 'TestMigration';

		// Vérifier que le répertoire de migrations existe
		$migrationsPath = __DIR__ . "/../../database/migrations";
		if (!is_dir($migrationsPath)) {
			mkdir($migrationsPath, 0777, true);
		}

		$cli->testCommand('make:migration', [$migrationName]);

		$timestamp = date('YmdHis');
		$migrationFile = "$migrationsPath/{$timestamp}_{$migrationName}.php";
		$this->assertFileExists($migrationFile);

		unlink($migrationFile); // Nettoyage après test
	}



    // Ajoutez d'autres tests pour les autres fonctions CLI
    private function cleanUpModule(string $moduleName): void {
        $basePath = __DIR__ . "/../../modules/$moduleName";
        if (file_exists($basePath)) {
            $this->deleteFilesInDir("$basePath/controllers");
            $this->deleteFilesInDir("$basePath/models");
            $this->deleteFilesInDir("$basePath/views/partials");
            $this->deleteFilesInDir("$basePath/views");
            $this->deleteFilesInDir("$basePath/middlewares");
            $this->deleteFilesInDir("$basePath/actions");
            $this->deleteFilesInDir("$basePath/config");

            @rmdir("$basePath/controllers");
            @rmdir("$basePath/models");
            @rmdir("$basePath/views/partials");
            @rmdir("$basePath/views");
            @rmdir("$basePath/middlewares");
            @rmdir("$basePath/actions");
            @rmdir("$basePath/config");
            @rmdir($basePath);
        }
    }

    private function deleteFilesInDir(string $dir): void {
        if (file_exists($dir)) {
            $files = glob("$dir/*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
	
	public function testSetConfig(): void {
		$cli = new CLI();
		$cli->testCommand('config:set', ['app.debug', 'true']);

		$config = include __DIR__ . '/../../config/config.php';
		$this->assertEquals('true', $config['app']['debug']);

		// Nettoyer après le test
		$cli->testCommand('config:set', ['app.debug', 'false']);
	}

	public function testGetConfig(): void {
		$cli = new CLI();
		$cli->testCommand('config:set', ['app.debug', 'true']);
		
		ob_start();
		$cli->testCommand('config:get', ['app.debug']);
		$output = ob_get_clean();

		$this->assertStringContainsString("Valeur de 'app.debug' : true", $output);

		// Nettoyer après le test
		$cli->testCommand('config:set', ['app.debug', 'false']);
	}
	
	public function testSetEnv(): void {
		$cli = new CLI();
		$cli->testCommand('env:set', ['APP_DEBUG', 'true']);

		$envContent = file_get_contents(__DIR__ . '/../../.env');
		$this->assertStringContainsString('APP_DEBUG=true', $envContent);

		// Nettoyer après le test
		$cli->testCommand('env:set', ['APP_DEBUG', 'false']);
	}

	public function testGetEnv(): void {
		$cli = new CLI();
		$cli->testCommand('env:set', ['APP_DEBUG', 'true']);
		
		ob_start();
		$cli->testCommand('env:get', ['APP_DEBUG']);
		$output = ob_get_clean();

		$this->assertStringContainsString("Valeur de 'APP_DEBUG' : true", $output);

		// Nettoyer après le test
		$cli->testCommand('env:set', ['APP_DEBUG', 'false']);
	}
	
}

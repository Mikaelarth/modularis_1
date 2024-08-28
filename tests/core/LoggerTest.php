<?php

namespace Tests\Core;

use Core\Logger;
use PHPUnit\Framework\TestCase;
use Monolog\Logger as MonoLogger;

class LoggerTest extends TestCase {

	protected function setUp(): void {
		$logPath = __DIR__ . '/../_data/test.log';

		if (!file_exists($logPath)) {
			touch($logPath);
			chmod($logPath, 0666);
		}

		if (!is_writable($logPath)) {
			echo "Fichier non accessible en écriture: $logPath\n";
		}

		if (!is_readable($logPath)) {
			echo "Fichier non accessible en lecture: $logPath\n";
		}

		Logger::init($logPath, MonoLogger::DEBUG);
	}


    // protected function tearDown(): void {
        // Effacer le fichier de log après chaque test
        // if (file_exists(__DIR__ . '/../_data/test.log')) {
            // unlink(__DIR__ . '/../_data/test.log');
        // }
    // }

    public function testLoggerInitialization() {
        $logger = Logger::getInstance();
        $this->assertInstanceOf(MonoLogger::class, $logger);
    }

    public function testInfoLog() {
        Logger::info('Test info message');
        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');
        $this->assertStringContainsString('Test info message', $logContent);
        $this->assertStringContainsString('INFO', $logContent);
    }

    public function testErrorLog() {
        Logger::error('Test error message');
        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');
        $this->assertStringContainsString('Test error message', $logContent);
        $this->assertStringContainsString('ERROR', $logContent);
    }

    public function testWarningLog() {
        Logger::warning('Test warning message');
        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');
        $this->assertStringContainsString('Test warning message', $logContent);
        $this->assertStringContainsString('WARNING', $logContent);
    }

    public function testDebugLog() {
        Logger::debug('Test debug message');
        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');
        $this->assertStringContainsString('Test debug message', $logContent);
        $this->assertStringContainsString('DEBUG', $logContent);
    }

    public function testLogWithContext() {
        Logger::info('Test context message', ['user' => 'john_doe']);
        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');
        $this->assertStringContainsString('Test context message', $logContent);
        $this->assertStringContainsString('"user":"john_doe"', $logContent);
    }

    public function testClearLog() {
        Logger::info('Message to be cleared');
        Logger::clear();
        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');
        $this->assertEmpty($logContent);
    }

    public function testLoggerRespectsLogLevel() {
        // Réinitialiser le logger avec un niveau de log ERROR
        Logger::init(__DIR__ . '/../_data/test.log', MonoLogger::ERROR);

        Logger::info('This message should not appear');
        Logger::error('This message should appear');

        $logContent = file_get_contents(__DIR__ . '/../_data/test.log');

        $this->assertStringContainsString('This message should appear', $logContent);
        $this->assertStringNotContainsString('This message should not appear', $logContent);
    }
}

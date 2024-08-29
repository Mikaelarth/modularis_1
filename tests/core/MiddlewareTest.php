<?php

namespace Tests\Core;

use Core\Middleware;
use PHPUnit\Framework\TestCase;

// Sous-classe concrète de Middleware pour les tests
class TestMiddleware extends Middleware
{
    public static function handle()
    {
        // Implémentation factice pour les tests
        return true;
    }
}

class MiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        // Réinitialiser l'expéditeur d'en-tête à chaque test
        Middleware::setHeaderSender('header');
    }

    public function testHandleMethodExists()
    {
        $this->assertTrue(method_exists(TestMiddleware::class, 'handle'), 'Method handle does not exist');
    }

	public function testRedirectFunctionality()
	{
		Middleware::setHeaderSender(function ($header) {
			$this->assertSame('Location: /new-url', $header);
		});

		// Définissez une constante de test
		define('TESTING_ENV', true);

		try {
			Middleware::redirect('/new-url');
		} catch (\Exception $e) {
			$this->assertSame('Exit called after redirect', $e->getMessage());
		}
	}



	public function testJsonResponseFunctionality()
	{
		Middleware::setHeaderSender(function ($header) {
			$this->assertSame('Content-Type: application/json', $header);
		});

		// Définissez une constante de test
		if (!defined('TESTING_ENV')) {
			define('TESTING_ENV', true);
		}

		try {
			Middleware::jsonResponse(['status' => 'success']);
		} catch (\Exception $e) {
			$this->assertSame('Exit called after jsonResponse', $e->getMessage());
		}

		// Vérifiez le contenu de la sortie
		$this->expectOutputString('{"status":"success"}');
	}



    public function testSetHeaderSenderWithInvalidCallable()
    {
        $this->expectException(\TypeError::class);

        // Ceci devrait lancer une TypeError car 'non_callable_value' n'est pas callable
        TestMiddleware::setHeaderSender('non_callable_value');
    }
}

<?php

namespace Tests\Core;

use Core\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        Router::resetRoutes();
        Router::setHeaderSender(function ($header) {
            // Pour les tests, ne rien faire avec les en-têtes
        });
        // Démarrer la capture de sortie
        ob_start();
    }

    protected function tearDown(): void
    {
        // Arrêter la capture de sortie
        ob_end_clean();
    }

    public function testAddRoute()
    {
        Router::get('/home', function () {
            echo 'Home';
        });

        Router::dispatch('/home', 'GET');

        // Capturer la sortie
        $output = ob_get_clean();
        // Redémarrer la capture de sortie après récupération
        ob_start();

        $this->assertEquals('Home', $output);
    }

    public function testPostRoute()
    {
        Router::post('/submit', function () {
            echo 'Form submitted';
        });

        Router::dispatch('/submit', 'POST');

        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('Form submitted', $output);
    }

    public function testRouteNotFound()
    {
        Router::dispatch('/unknown', 'GET');
        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('404 - Page Not Found', $output);
    }

    public function testRouteWithParameters()
    {
        Router::get('/user/{id}', function ($id) {
            echo "User ID: $id";
        });

        Router::dispatch('/user/42', 'GET');

        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('User ID: 42', $output);
    }

    public function testGroupRoutes()
    {
        Router::group('/admin', function () {
            Router::get('/dashboard', function () {
                echo 'Admin Dashboard';
            });
        });

        Router::dispatch('/admin/dashboard', 'GET');

        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('Admin Dashboard', $output);
    }

    public function testMiddlewares()
    {
        $middlewareExecuted = false;

        Router::get('/profile', function () use (&$middlewareExecuted) {
            $middlewareExecuted = true;
        }, [
            function () use (&$middlewareExecuted) {
                $middlewareExecuted = true;
                return true;
            }
        ]);

        Router::dispatch('/profile', 'GET');

        $this->assertTrue($middlewareExecuted, 'Middleware should be executed');
    }

    public function testRedirect()
    {
        Router::setHeaderSender(function ($header) {
            echo $header;
        });

        Router::get('/old-route', function () {
            Router::redirect('/new-route');
        });

        Router::dispatch('/old-route', 'GET');

        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('Location: /new-route', $output);
    }

    public function testJsonResponse()
    {
        Router::setHeaderSender(function ($header) {
            echo $header;
        });

        Router::get('/data', function () {
            Router::json(['success' => true]);
        });

        Router::dispatch('/data', 'GET');

        $output = ob_get_clean();
        ob_start();

        $this->assertEquals("Content-Type: application/json\n{\"success\":true}", $output);
    }

    public function testInvalidMethod()
    {
        Router::get('/only-get', function () {
            echo 'Should not execute';
        });

        Router::dispatch('/only-get', 'POST');
        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('404 - Page Not Found', $output);
    }

    public function testRouteWithoutHandler()
    {
        Router::get('/no-handler', null);
        Router::dispatch('/no-handler', 'GET');

        $output = ob_get_clean();
        ob_start();

        $this->assertEquals('404 - Page Not Found', $output);
    }
}

<?php

use PHPUnit\Framework\TestCase;
use Core\View;

class ViewTest extends TestCase
{
    protected string $viewsPath = __DIR__ . '/views/'; // Chemin de test pour les vues
    protected string $layoutsPath = __DIR__ . '/layouts/'; // Chemin de test pour les layouts

    protected function setUp(): void
    {
        // Définir les chemins personnalisés pour les tests
        View::setViewsPath($this->viewsPath);
        View::setLayoutsPath($this->layoutsPath);

        // Créer des fichiers de vue et de layout de test
        if (!is_dir($this->viewsPath)) mkdir($this->viewsPath, 0777, true);
        if (!is_dir($this->layoutsPath)) mkdir($this->layoutsPath, 0777, true);

        file_put_contents($this->viewsPath . 'testView.php', "<div>Test View Content</div>");
        file_put_contents($this->layoutsPath . 'testLayout.php', "<html><body>{{content}}</body></html>");
    }

    protected function tearDown(): void
    {
        // Supprimer les fichiers de test après les tests
        array_map('unlink', glob($this->viewsPath . '*.*'));
        array_map('unlink', glob($this->layoutsPath . '*.*'));
        rmdir($this->viewsPath);
        rmdir($this->layoutsPath);
    }

    public function testRenderView()
    {
        ob_start();
        View::render('testView');
        $output = ob_get_clean();

        $this->assertStringContainsString('Test View Content', $output);
    }

    public function testRenderViewWithLayout()
    {
        ob_start();
        View::render('testView', [], 'testLayout');
        $output = ob_get_clean();

        $this->assertStringContainsString('<html><body><div>Test View Content</div></body></html>', $output);
    }

    public function testRenderPartial()
    {
        file_put_contents($this->viewsPath . 'partialView.php', "<span>Partial View Content</span>");

        ob_start();
        View::renderPartial('partialView');
        $output = ob_get_clean();

        $this->assertStringContainsString('Partial View Content', $output);
    }

    public function testRenderViewNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('La vue nonExistentView n\'existe pas.');

        View::render('nonExistentView');
    }

    public function testRenderLayoutNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Le layout nonExistentLayout n\'existe pas.');

        View::render('testView', [], 'nonExistentLayout');
    }

    public function testEscapeData()
    {
        $data = ['key' => '<script>alert("XSS")</script>'];

        $escapedData = self::invokePrivateMethod('escapeData', [$data]);

        $this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $escapedData['key']);
    }

    private static function invokePrivateMethod(string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(View::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $parameters);
    }
}

<?php
// tests/core/EnvTest.php

namespace Tests\Core;

use Core\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase {

    protected function setUp(): void {
        Env::reset(); // Réinitialiser avant chaque test
    }

    public function testLoadValidEnvFile() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        $this->assertEquals('localhost', Env::get('DB_HOST'));
        $this->assertEquals('root', Env::get('DB_USER'));
        $this->assertEquals(3306, Env::get('DB_PORT'));
        $this->assertTrue(Env::get('FEATURE_ENABLED'));
        $this->assertFalse(Env::get('FEATURE_DISABLED'));
    }

    public function testInvalidKeyThrowsException() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Clé d'environnement invalide détectée : INVALID KEY");
        Env::load(__DIR__ . '/../_data/.env.invalid_key');
    }

    public function testGetWithDefault() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        $this->assertEquals('default_value', Env::get('NON_EXISTENT_KEY', 'default_value'));
    }

    public function testSetEnvVariable() {
        Env::set('CUSTOM_KEY', 'custom_value');
        $this->assertEquals('custom_value', Env::get('CUSTOM_KEY'));
    }

    public function testSetInvalidEnvVariable() {
        Env::set('INVALID_KEY', 'value');
        // La clé invalide ne doit pas être ajoutée, donc 'get' doit retourner null
         $this->assertNull(Env::get('INVALID KEY'), "La clé invalide ne devrait pas être définie dans les variables d'environnement.");
    }

    public function testAllEnvVariables() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        $envVars = Env::all();
        $this->assertArrayHasKey('DB_HOST', $envVars);
        $this->assertArrayHasKey('DB_USER', $envVars);
    }

    public function testResetEnvVariables() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        Env::reset();
        $this->assertNull(Env::get('DB_HOST'));
        $this->assertNull(Env::get('DB_USER'));
    }

    public function testParsingBooleanValues() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        $this->assertTrue(Env::get('FEATURE_ENABLED'));
        $this->assertFalse(Env::get('FEATURE_DISABLED'));
    }

    public function testParsingNumericValues() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        $this->assertEquals(12345, Env::get('NUMERIC_VALUE'));
        $this->assertEquals(12.34, Env::get('FLOAT_VALUE'));
    }

    public function testNoReloadOfEnvVariables() {
        Env::load(__DIR__ . '/../_data/.env.valid');
        Env::set('DB_HOST', 'new_host');
        $this->assertEquals('new_host', Env::get('DB_HOST'));
    }
}

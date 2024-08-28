<?php
// tests/core/DatabaseTest.php

namespace Tests\Core;

use Core\Database;
use PHPUnit\Framework\TestCase;
use PDO;
use Exception;

class DatabaseTest extends TestCase {

    protected function setUp(): void {
        Database::init([
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'test_db',
            'username' => 'root',
            'password' => 'yF_FAMILLEY1983',
            'port' => 3306,
            'charset' => 'utf8'
        ]);
    }

    public function testConnectionIsEstablished() {
        $this->assertInstanceOf(PDO::class, Database::getInstance());
    }

    public function testQueryExecution() {
        $result = Database::query('SELECT 1');
        $this->assertEquals([['1' => '1']], $result);
    }

    public function testExecuteMethod() {
        $query = "INSERT INTO test_table (columns) VALUES (:columns)";
        $result = Database::execute($query, ['columns' => 'test_value']);
        $this->assertEquals(1, $result);
    }

    public function testTransactionHandling() {
        Database::beginTransaction();
        Database::execute('UPDATE test_table SET columns = ? WHERE id = ?', ['transacted_value', 1]);
        Database::rollback();

        $result = Database::query('SELECT columns FROM test_table WHERE id = ?', [1]);
        $this->assertNotEquals('transacted_value', $result[0]['columns']);
    }

    public function testLastInsertId() {
        Database::execute('INSERT INTO test_table (columns) VALUES (?)', ['test_value']);
        $lastId = Database::lastInsertId();
        $this->assertNotEmpty($lastId);
    }

    public function testExceptionOnInvalidQuery() {
        $this->expectException(Exception::class);
        Database::query('INVALID SQL');
    }
}

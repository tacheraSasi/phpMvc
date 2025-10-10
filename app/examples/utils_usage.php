<?php
/**
 * Examples of using the new Utils, Facades, and Helpers
 * This file demonstrates the features added to the framework
 */

define('ROOTPATH', __DIR__ . '/../../public/');
require_once '../core/__init__.php';

echo "=== PHP MVC Framework - Utils, Facades & Helpers Examples ===\n\n";

// ==================== String Utilities ====================
echo "--- String Utilities ---\n";

use Core\Utils\StringUtil;

echo "UUID: " . StringUtil::uuid() . "\n";
echo "Random: " . StringUtil::random(16) . "\n";
echo "Slug: " . StringUtil::slug('Hello World PHP') . "\n";
echo "CamelCase: " . StringUtil::camelCase('hello-world') . "\n";
echo "SnakeCase: " . StringUtil::snakeCase('HelloWorld') . "\n";
echo "Truncate: " . StringUtil::truncate('This is a long string', 10) . "\n\n";

// ==================== Array Utilities ====================
echo "--- Array Utilities ---\n";

use Core\Utils\ArrayUtil;

$data = [
    'user' => [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'profile' => [
            'age' => 30
        ]
    ]
];

echo "Get nested: " . ArrayUtil::get($data, 'user.profile.age', 'N/A') . "\n";
echo "Has key: " . (ArrayUtil::has($data, 'user.email') ? 'true' : 'false') . "\n";

$numbers = [1, [2, 3], [4, [5, 6]]];
echo "Flatten: " . json_encode(ArrayUtil::flatten($numbers)) . "\n\n";

// ==================== Date Utilities ====================
echo "--- Date Utilities ---\n";

use Core\Utils\DateUtil;

echo "Current: " . DateUtil::currentDate() . "\n";
echo "Format: " . DateUtil::format('2024-01-01', 'd/m/Y') . "\n";
echo "Time ago: " . DateUtil::timeAgo('2024-01-01') . "\n";
echo "Add days: " . DateUtil::addDays('2024-01-01', 7) . "\n";
echo "ISO8601: " . DateUtil::toIso8601('2024-01-01') . "\n\n";

// ==================== File Utilities ====================
echo "--- File Utilities ---\n";

use Core\Utils\FileUtil;

echo "Extension: " . FileUtil::extension('test.php') . "\n";
echo "Basename: " . FileUtil::basename('/path/to/file.txt') . "\n";
echo "Human size: " . FileUtil::humanFileSize(1024000) . "\n\n";

// ==================== Crypto Utilities ====================
echo "--- Crypto Utilities ---\n";

use Core\Utils\CryptoUtil;

$password = 'secret123';
$hash = CryptoUtil::hashPassword($password);
echo "Hash password: " . substr($hash, 0, 20) . "...\n";
echo "Verify password: " . (CryptoUtil::verifyPassword($password, $hash) ? 'true' : 'false') . "\n";
echo "Random token: " . substr(CryptoUtil::randomToken(), 0, 20) . "...\n";
echo "API key: " . CryptoUtil::generateApiKey('test', 16) . "\n\n";

// ==================== Helper Functions ====================
echo "--- Helper Functions ---\n";

echo "str_slug: " . str_slug('Hello World PHP') . "\n";
echo "str_random: " . str_random(10) . "\n";
echo "str_camel: " . str_camel('hello-world') . "\n";
echo "str_snake: " . str_snake('HelloWorld') . "\n";
echo "str_uuid: " . str_uuid() . "\n\n";

// Array helpers
$arr = ['name' => 'John', 'email' => 'john@test.com', 'age' => 30];
echo "array_only: " . json_encode(array_only($arr, ['name', 'email'])) . "\n";
echo "array_except: " . json_encode(array_except($arr, ['age'])) . "\n\n";

echo "=== All Examples Completed Successfully ===\n";

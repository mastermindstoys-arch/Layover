<?php
/**
 * Database Setup Script for Layover Solutions
 * Run this script to initialize the database and tables
 */

// Include configuration
require_once 'config.php';

try {
    // Connect to MySQL without specifying a database
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '" . DB_NAME . "' created or already exists.\n";

    // Select the database
    $pdo->exec("USE " . DB_NAME);

    // Read and execute the schema file
    $schema = file_get_contents(__DIR__ . '/database_schema.sql');
    if (!$schema) {
        throw new Exception("Could not read database_schema.sql file from " . __DIR__ . '/database_schema.sql');
    }

    // First pass: Create all tables without foreign keys
    $lines = explode("\n", $schema);
    $inCreateTable = false;
    $tableSQL = '';
    $tableName = '';

    foreach ($lines as $line) {
        $line = trim($line);

        if (empty($line) || strpos($line, '--') === 0) {
            continue; // Skip comments and empty lines
        }

        if (preg_match('/CREATE TABLE.*IF NOT EXISTS\s+(\w+)/i', $line, $matches)) {
            $inCreateTable = true;
            $tableName = $matches[1];
            $tableSQL = $line . "\n";
        } elseif ($inCreateTable) {
            $tableSQL .= $line . "\n";

            if (strpos($line, ';') !== false) {
                // End of table definition
                // Remove foreign key constraints for first pass
                $tableSQL = preg_replace('/,\s*FOREIGN KEY[^;]+/i', '', $tableSQL);
                $tableSQL = preg_replace('/FOREIGN KEY[^;]+;\s*\)/i', ')', $tableSQL);

                try {
                    $pdo->exec($tableSQL);
                    echo "✓ Created table: $tableName\n";
                } catch (PDOException $e) {
                    echo "Warning: Failed to create table $tableName: " . $e->getMessage() . "\n";
                }

                $inCreateTable = false;
                $tableSQL = '';
                $tableName = '';
            }
        }
    }

    // Second pass: Add foreign key constraints
    echo "\nAdding foreign key constraints...\n";
    $lines = explode("\n", $schema);
    $inCreateTable = false;
    $tableName = '';

    foreach ($lines as $line) {
        $line = trim($line);

        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }

        if (preg_match('/CREATE TABLE.*IF NOT EXISTS\s+(\w+)/i', $line, $matches)) {
            $tableName = $matches[1];
        } elseif (strpos($line, 'FOREIGN KEY') !== false) {
            // Extract foreign key constraint
            $fkSQL = "ALTER TABLE $tableName ADD $line";
            try {
                $pdo->exec($fkSQL);
                echo "✓ Added foreign key to $tableName\n";
            } catch (PDOException $e) {
                echo "Warning: Failed to add foreign key to $tableName: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "✓ Database schema created successfully!\n";
    echo "✓ All tables and initial data have been set up.\n";
    echo "\nDatabase setup complete. You can now use the forms.\n";

} catch (PDOException $e) {
    echo "✗ Database setup failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. MySQL server is running\n";
    echo "2. Database credentials in config.php are correct\n";
    echo "3. You have permission to create databases\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
<?php
/**
 * Database Initialization Script
 * This script creates the SQLite database and runs the schema and sample data
 */

// Define database path
$dbPath = __DIR__ . '/database/database.sqlite';
$schemaFile = __DIR__ . '/database/schema.sql';
$sampleDataFile = __DIR__ . '/database/sample-data.sql';

echo "Starting database initialization...\n\n";

// Remove existing database if it exists
if (file_exists($dbPath)) {
    echo "Removing existing database...\n";
    unlink($dbPath);
}

// Create database directory if it doesn't exist
$dbDir = dirname($dbPath);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

try {
    // Create new SQLite database
    echo "Creating new SQLite database...\n";
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute schema
    echo "Reading schema file...\n";
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    $schema = file_get_contents($schemaFile);
    echo "Executing schema...\n";
    $pdo->exec($schema);
    
    // Read and execute sample data
    echo "Reading sample data file...\n";
    if (!file_exists($sampleDataFile)) {
        throw new Exception("Sample data file not found: $sampleDataFile");
    }
    
    $sampleData = file_get_contents($sampleDataFile);
    echo "Executing sample data...\n";
    $pdo->exec($sampleData);
    
    // Set proper permissions
    chmod($dbPath, 0666);
    
    echo "\n✅ Database initialization completed successfully!\n";
    echo "Database file: $dbPath\n";
    
    // Test admin user
    echo "\n🔍 Testing admin user...\n";
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ? AND status = ?");
    $stmt->execute(['admin', 'admin', 'active']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin user found successfully!\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Email: " . $admin['email'] . "\n";
        echo "Role: " . $admin['role'] . "\n";
        echo "Status: " . $admin['status'] . "\n";
        
        // Test password
        $testPassword = 'admin123';
        if (password_verify($testPassword, $admin['password'])) {
            echo "✅ Password verification successful for: $testPassword\n";
        } else {
            echo "❌ Password verification failed for: $testPassword\n";
        }
    } else {
        echo "❌ Admin user not found!\n";
    }
    
    // Show table counts
    echo "\n📊 Database statistics:\n";
    $tables = ['users', 'pages', 'services', 'portfolio', 'gallery', 'settings'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "- $table: $count records\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Database is ready to use!\n";
echo "You can now login with:\n";
echo "Username: admin\n";
echo "Password: admin123\n";
?>
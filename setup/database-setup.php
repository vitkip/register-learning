<?php
/**
 * Database Setup Script
 * Run this script to initialize the database with required tables and sample data
 */

require_once '../config/config.php';
require_once '../config/database.php';

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    echo "✓ Database connection successful\n";
} catch (PDOException $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

echo "Setting up Register-Learning database...\n\n";

// Read and execute complete schema
echo "1. Creating database tables...\n";
$schemaSQL = file_get_contents('../database/complete-schema.sql');

// Split SQL by statements (simple approach)
$statements = explode(';', $schemaSQL);

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement) && !preg_match('/^(\/\*|--|\!)/', $statement)) {
        try {
            $db->exec($statement);
        } catch (PDOException $e) {
            // Ignore table exists errors and similar
            if (strpos($e->getMessage(), 'already exists') === false && 
                strpos($e->getMessage(), 'Duplicate') === false) {
                echo "Warning: " . $e->getMessage() . "\n";
            }
        }
    }
}
echo "✓ Database tables created successfully\n\n";

// Insert sample data
echo "2. Inserting sample data...\n";
$sampleSQL = file_get_contents('../database/sample-data.sql');

// Split and execute sample data statements
$statements = explode(';', $sampleSQL);

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement) && !preg_match('/^(\/\*|--|\!)/', $statement)) {
        try {
            $db->exec($statement);
        } catch (PDOException $e) {
            // Ignore duplicate entry errors
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "Warning: " . $e->getMessage() . "\n";
            }
        }
    }
}
echo "✓ Sample data inserted successfully\n\n";

// Verify setup
echo "3. Verifying setup...\n";

$tables = ['users', 'students', 'majors', 'academic_years', 'subjects', 'settings'];
foreach ($tables as $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   $table: $count records\n";
    } catch (PDOException $e) {
        echo "   ✗ $table: Error - " . $e->getMessage() . "\n";
    }
}

echo "\n✓ Database setup completed successfully!\n\n";

echo "Default admin login:\n";
echo "Username: admin\n";
echo "Password: password\n\n";

echo "You can now access the application at: " . BASE_URL . "\n";
?>
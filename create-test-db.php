<?php

// Script para crear la base de datos de pruebas
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'sistema_agnes_test';

try {
    // Conectar sin especificar base de datos
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    echo "✅ Base de datos '$database' creada exitosamente.\n";
    
    // Verificar que se creó
    $result = $pdo->query("SHOW DATABASES LIKE '$database'");
    if ($result->rowCount() > 0) {
        echo "✅ Base de datos '$database' existe y está disponible.\n";
    } else {
        echo "❌ Error: No se pudo crear la base de datos.\n";
        exit(1);
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "💡 Asegúrate de que MySQL esté ejecutándose y las credenciales sean correctas.\n";
    exit(1);
}

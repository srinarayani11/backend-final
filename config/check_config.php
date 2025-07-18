<?php

foreach (glob(__DIR__ . "/config/*.php") as $file) {
    $value = include $file;
    if (!is_array($value)) {
        echo "❌ Invalid config in: " . basename($file) . " (type: " . gettype($value) . ")" . PHP_EOL;
    } else {
        echo "✅ " . basename($file) . " is valid." . PHP_EOL;
    }
}

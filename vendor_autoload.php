<?php

/**
 * Autoloader minimaliste (PSR-4-like) pour eviter une dependance a Composer.
 * Mappe les namespaces `App\` et `Config\` vers leurs dossiers respectifs.
 *
 * Si vous preferez Composer, vous pouvez remplacer ce fichier par
 * `require __DIR__ . '/vendor/autoload.php';` apres avoir declare le
 * mapping PSR-4 correspondant dans composer.json.
 */

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\'    => __DIR__ . '/app/',
        'Config\\' => __DIR__ . '/config/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($prefix, $class, strlen($prefix)) === 0) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

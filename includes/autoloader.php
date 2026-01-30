<?php

namespace PAC\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Autoloader {

    /**
     * Base namespace
     *
     * @var string
     */
    private $prefix;

    /**
     * Base directory where classes are located
     *
     * @var string
     */
    private $base_dir;

    /**
     * Constructor
     *
     * @param string $prefix   Namespace prefix, e.g., 'PAC\\'
     * @param string $base_dir Base directory for class files
     */
    public function __construct(string $prefix, string $base_dir) {
        $this->prefix = $prefix;
        $this->base_dir = rtrim($base_dir, '/') . '/';
    }

    /**
     * Register autoloader with SPL
     */
    public function register(): void {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Load a class file
     *
     * @param string $class Fully qualified class name
     */
    public function loadClass(string $class): void {
        // Only load classes from our namespace
        $len = strlen($this->prefix);
        if (strncmp($this->prefix, $class, $len) !== 0) {
            return;
        }

        // Get relative class name
        $relative_class = substr($class, $len);
        // Convert namespace separators to directory separators
        $file = $this->base_dir . str_replace([ '\\' , '_' ], [ '/' , '-' ], $relative_class);

        // WordPress style: lowercase + class- prefix
        $file = dirname($file) . '/class-' . strtolower(basename($file)) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}

<?php
namespace Bee\Core;

use Bee\Exception\CoreException;

class Loader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected static $prefixes = [];

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register([static::class, 'loadClass']);
    }

    public static function load($filename)
    {
        if (is_file($filename)) {
            return include $filename;
        } else {
            throw new CoreException('Can not find the file: ' . $filename);
        }
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     * namespace.
     * @param bool $prepend If true, prepend the base directory to the stack
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public static function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // 规范化命名空间前缀
        $prefix = trim($prefix, '\\') . '\\';

        // 初始化命名空间前缀数组
        if (isset(self::$prefixes[$prefix]) === false) {
            self::$prefixes[$prefix] = [];
        }

        // 将命名空间前缀与文件基目录对插入保存数组
        if ($prepend) {
            array_unshift(self::$prefixes[$prefix], $base_dir);
        } else {
            array_push(self::$prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public static function loadClass($class)
    {
        // the current namespace prefix
        $prefix = $class;
    
        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== $pos = strpos($prefix, '\\')) {
    
            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);
    
            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);
    
            // try to load a mapped file for the prefix and relative class
            $mapped_file = self::loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }
    
            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');
        }
    
        // never found a mapped file
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected static function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        if (isset(self::$prefixes[$prefix]) === false) {
            return false;
        }
    
        // look through base directories for this namespace prefix
        foreach (self::$prefixes[$prefix] as $base_dir) {
    
            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
            . str_replace('\\', '/', $relative_class)
            . '.php';
    
            // if the mapped file exists, require it
            if (self::requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }
    
        // never found it
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     * 
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected static function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
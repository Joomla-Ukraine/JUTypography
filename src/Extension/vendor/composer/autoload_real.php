<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit609dc1ee14b79f1dd473ef16de0e2b18
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit609dc1ee14b79f1dd473ef16de0e2b18', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit609dc1ee14b79f1dd473ef16de0e2b18', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit609dc1ee14b79f1dd473ef16de0e2b18::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}

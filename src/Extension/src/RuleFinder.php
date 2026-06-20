<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class RuleFinder
{
    /**
     * @var string[]
     */
    public static $rules = [];

    /**
     * @return string[]
     *
     * @throws \ReflectionException
     */
    public static function getAllRule(): array
    {
        if (empty(static::$rules)) {
            $baseClass = new ReflectionClass('JUTypography\Rule\AbstractRule');

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    __DIR__.DIRECTORY_SEPARATOR.'Rule', RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach ($files as $file) {
                $className = static::getClassNameByFilePath($file->getPathname());
                if (class_exists($className)) {
                    $reflectionClass = new ReflectionClass($className);
                    if ($reflectionClass->isSubclassOf($baseClass)) {
                        static::$rules[] = $className;
                    }
                }
            }
        }

        return static::$rules;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected static function getClassNameByFilePath(string $path): string
    {
        $class = str_replace([__DIR__, '.php'], [
            'JUTypography',
            '',
        ], $path);

        return str_replace('/', '\\', $class);
    }
}

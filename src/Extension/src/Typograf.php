<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography;

use JUTypography\Rule\AbstractRule;
use RuntimeException;

class Typograf
{
    /**
     * @var Rule\AbstractRule[]
     */
    protected $rules = [];

    /**
     * @var Debug
     */
    protected $debug;

    /**
     * @var SafeBlock
     */
    protected $safeBlock;

    /**
     * @throws \ReflectionException
     */
    public function __construct(bool $debug = false)
    {
        if (true === $debug) {
            $this->debug = new Debug();
        }

        $this->safeBlock = new SafeBlock();
        $this->initRules();
    }

    public function addRule(object $ruleClass): void
    {
        if (true === is_subclass_of(
                $ruleClass,
                AbstractRule::class
            )) {
            $this->rules[ spl_object_hash($ruleClass) ] = $ruleClass;
            $this->sortRule();
        }
    }

    /**
     * @param   mixed  $text
     *
     * @return string
     */
    public function apply(mixed $text): string
    {
        $text = (string)$text;
        if (null !== $this->debug) {
            $this->debug->setStartValue($text);
        }

        $newText = $this->safeBlock->on($text);

        foreach ($this->rules as $rule) {
            $startText = $newText;
            if (false === $rule->getActive()) {
                if (null !== $this->debug) {
                    $this->debug->addTrace(
                        $rule,
                        $startText,
                        $newText,
                        Debug::STATUS_DEACTIVATE
                    );
                }

                continue;
            }

            $newText = $rule->handler($newText);

            if (null !== $this->debug) {
                $this->debug->addTrace(
                    $rule,
                    $startText,
                    $newText,
                    $startText !== $newText ? Debug::STATUS_MODIFY : Debug::STATUS_NOT_MODIFY
                );
            }
        }

        $newText = $this->safeBlock->off($newText);

        if (null !== $this->debug) {
            $this->debug->setEndValue($newText);
        }

        return $newText;
    }

    public function enableRule(string $name): void
    {
        $this->changeRule($name, true);
    }

    public function disableRule(string $name): void
    {
        $this->changeRule($name, false);
    }

    /**
     * @return Rule\AbstractRule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function getSafeBlock(): SafeBlock
    {
        return $this->safeBlock;
    }

    /**
     * @throws \Exception
     */
    public function getDebug(): Debug
    {
        if (null !== $this->debug) {
            return $this->debug;
        }

        throw new RuntimeException('Debug mode not enable');
    }

    /**
     * @throws \ReflectionException
     */
    protected function initRules(): void
    {
        $all = RuleFinder::getAllRule();

        foreach ($all as $ruleClass) {
            /**
             * @var Rule\AbstractRule $ruleObj
             */
            $ruleObj = new $ruleClass();

            $this->rules[ $ruleClass ] = $ruleObj;
        }

        $this->sortRule();
    }

    protected function sortRule(): void
    {
        uasort($this->rules, static function ($a, $b)
        {
            return $a->getSort() <=> $b->getSort();
        });
    }

    final protected function changeRule(string $name, bool $active): void
    {
        if ('*' === $name) {
            foreach ($this->rules as $rule) {
                $rule->setActive($active);
            }
        } else {
            $pattern = '#'.str_replace(['\\', '*'], [
                    '\\\\',
                    '.+',
                ], $name).'$#';
            foreach ($this->rules as $rule) {
                preg_match($pattern, get_class($rule), $matches);
                if (!empty($matches)) {
                    $rule->setActive($active);
                }
            }
        }
    }
}

<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule;

abstract class AbstractRule
{
    /**
     * @var string
     */
    public string $name = 'Name rule';

    /**
     * @var array
     */
    protected array $char = [
        'allQuote' => '«‹»›„“‟”"\'',
        'allDash' => '-|‒|–|—',
        'charEnd' => '.,!?:;',
        'char' => 'а-яєїґa-z',
        'charCase' => 'а-яєїґa-zA-ЯЄЇҐA-Z',
        'nbsp' => '&nbsp;',
        'dash' => '&mdash;',
    ];

    /**
     * @var bool
     */
    protected bool $active = true;

    /**
     * @var int
     */
    protected int $sort = 500;

    /**
     * @var array
     */
    protected array $settings = [];

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setSetting(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    abstract public function handler(string $text): string;
}

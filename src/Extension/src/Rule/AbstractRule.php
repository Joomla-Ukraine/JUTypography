<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule;

abstract class AbstractRule
{
	/**
	 * @var string
	 */
	public $name = 'Name rule';

	/**
	 * @var mixed[]
	 */
	protected $char = [
		'allQuote' => '«‹»›„“‟”"\'',
		'allDash'  => '-|‒|–|—',
		'charEnd'  => '.,!?:;',
		'char'     => 'а-яєїґa-z',
		'charCase' => 'а-яєїґa-zA-ЯЄЇҐA-Z',
		'nbsp'     => '&nbsp;',
		'dash'     => '&mdash;',
	];

	/**
	 * @var bool
	 */
	protected $active = true;

	/**
	 * @var int
	 */
	protected $sort = 500;

	/**
	 * @var mixed[]
	 */
	protected $settings = [];

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
	 * @param mixed[] $settings
	 */
	public function setSettings(array $settings): void
	{
		$this->settings = array_merge($this->settings, $settings);
	}

	/**
	 * @param mixed $value
	 */
	public function setSetting(string $key, $value): void
	{
		$this->settings[ $key ] = $value;
	}

	/**
	 * @return mixed[]
	 */
	public function getSettings(): array
	{
		return $this->settings;
	}

	abstract public function handler(string $text): string;
}

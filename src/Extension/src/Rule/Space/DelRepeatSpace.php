<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Space;

use JUTypography\Rule\AbstractRule;

class DelRepeatSpace extends AbstractRule
{
	public $name = 'Del Repeat Space';

	protected $sort = -100;

	public function handler(string $text): string
	{
		$pattern = '#[ \t]{2,}#u';

		$replace = ' ';

		return preg_replace($pattern, $replace, $text);
	}
}

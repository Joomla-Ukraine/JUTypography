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

class DelRepeatN extends AbstractRule
{
	public $name = 'Del Repeat N';

	protected $sort = -100;

	public function handler(string $text): string
	{
		$pattern = '#\n{2,}#u';

		$replace = "\n";

		return preg_replace($pattern, $replace, $text);
	}
}

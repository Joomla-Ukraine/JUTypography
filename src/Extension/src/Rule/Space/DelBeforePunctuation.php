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

class DelBeforePunctuation extends AbstractRule
{
	public $name = 'Del Before Punctuation';

	protected $sort = 300;

	public function handler(string $text): string
	{
		$pattern = '#((\s|&nbsp;)+)([' . $this->char[ 'charEnd' ] . '])(\s+|$)#iu';

		$replace = '$3$4';

		return preg_replace($pattern, $replace, $text);
	}
}

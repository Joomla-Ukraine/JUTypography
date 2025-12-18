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

class AfterPunctuation extends AbstractRule
{
	public $name = 'After Punctuation';

	protected $sort = 300;

	public function handler(string $text): string
	{
		$pattern = '#(\s|&nbsp;|^)([' . $this->char[ 'char' ] . '0-9]+)(\s|&nbsp;)?(:|\)|,|&hellip;|[!?]+)([' . $this->char[ 'char' ] . '])#iu';

		$replace = '$1$2$4 $5';

		return preg_replace($pattern, $replace, $text);
	}
}

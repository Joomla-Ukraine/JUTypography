<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Punctuation;

use JUTypography\Rule\AbstractRule;

class ExclamationQuestion extends AbstractRule
{
	public $name = 'Exclamation Question';

	public $sort = 800;

	public function handler(string $text): string
	{
		$pattern = '#([' . $this->char[ 'char' ] . '])!\?(\s|$|<)#iu';

		$replace = '$1?!$2';

		return preg_replace($pattern, $replace, $text);
	}
}

<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Symbol;

use JUTypography\Rule\AbstractRule;

class Copy extends AbstractRule
{
	public $name = 'Copy ©, TM ™,®';

	public function handler(string $text): string
	{
		$pattern = [
			'#\(r\)#iu',
			'#(copyright )?\((c|с)\)#iu',
			'#\(tm\)#iu',
		];

		$replace = [
			'&reg;',
			'&copy;',
			'&trade;',
		];

		return preg_replace($pattern, $replace, $text);
	}
}

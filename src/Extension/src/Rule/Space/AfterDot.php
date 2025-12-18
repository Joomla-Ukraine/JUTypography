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

class AfterDot extends AbstractRule
{
	public $name = 'After Dot';

	protected $sort = 300;

	public function handler(string $text): string
	{
		$pattern = '#([' . $this->char[ 'char' ] . '0-9]{2,})\.([А-ЯЁA-Z\n])#u';

		$replace = '$1. $2';

		return preg_replace($pattern, $replace, $text);
	}
}

<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Nbsp;

use JUTypography\Rule\AbstractRule;

class DayMonth extends AbstractRule
{
	public $name = 'Day Month';

	public function handler(string $text): string
	{
		$pattern = '#(\d{1,2}) (' . $this->char[ 'monthShort' ] . ')#iu';
		$replace = '$1' . $this->char[ 'nbsp' ] . '$2';

		return preg_replace($pattern, $replace, $text);
	}
}

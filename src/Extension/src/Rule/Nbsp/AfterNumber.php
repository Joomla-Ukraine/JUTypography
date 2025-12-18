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

class AfterNumber extends AbstractRule
{
	public $name = 'After Number';

	protected $settings = [
		'maxLen' => 5,
	];

	public function handler(string $text): string
	{
		$pattern = '#(^|\D)(\d{1,' . $this->settings[ 'maxLen' ] . '}) ([' . $this->char[ 'char' ] . ']+)#iu';
		$replace = '$1$2' . $this->char[ 'nbsp' ] . '$3';

		return preg_replace($pattern, $replace, $text);
	}
}

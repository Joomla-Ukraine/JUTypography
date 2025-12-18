<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Number;

use JUTypography\Rule\AbstractRule;

class DimensionSup extends AbstractRule
{
	public $name = 'Dimension Sup';

	public function handler(string $text): string
	{
		$pattern = '#(м|мм|см|дм|км|гм|m|km|dm|cm|mm)([\d]{1,3})([^' . $this->char[ 'char' ] . '0-9]|$)#iu';
		$replace = '$1<sup>$2</sup>$3';

		return preg_replace($pattern, $replace, $text);
	}
}

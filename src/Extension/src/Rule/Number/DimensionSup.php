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
		$superscriptMap = [
			'0' => '⁰',
			'1' => '¹',
			'2' => '²',
			'3' => '³',
			'4' => '⁴',
			'5' => '⁵',
			'6' => '⁶',
			'7' => '⁷',
			'8' => '⁸',
			'9' => '⁹',
		];

		$pattern = '#(м|мм|см|дм|км|гм|m|km|dm|cm|mm)([\d]{1,3})([^' . $this->char[ 'char' ] . '0-9]|$)#iu';

		return preg_replace_callback($pattern, function ($matches) use ($superscriptMap)
		{
			$unit  = $matches[ 1 ];
			$power = $matches[ 2 ];
			$after = $matches[ 3 ];

			$superPower = strtr($power, $superscriptMap);

			return $unit . $superPower . $after;
		}, $text);
	}
}
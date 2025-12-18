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

class Initials extends AbstractRule
{
	public $name = 'Initials';

	public function handler(string $text): string
	{
		$pattern = '#(^|[\s>' . $this->char[ 'allQuote' ] . '])(\p{Lu}\.)\s?(\p{Lu}\.)\s?(\p{Lu}\p{Ll}+)#iu';
		$replace = '$1$2' . $this->char[ 'nbsp' ] . '$3' . $this->char[ 'nbsp' ] . '$4';

		return preg_replace($pattern, $replace, $text);
	}
}

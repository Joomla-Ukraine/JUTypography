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

class DelDouble extends AbstractRule
{
	public $name = 'Del Double';

	protected $settings = [
		'maxLenMark' => 3,
	];

	public function handler(string $text): string
	{
		$pattern = [
			'#(,){2,}#iu',
			'#(:){2,}#iu',
			'#(&\w+;;);+|((^|\s)(\w+;));+#iu',
			'#(\.){4,}#iu',
			'#(!){' . ($this->settings[ 'maxLenMark' ] + 1) . ',}#iu',
			'#(\?){' . ($this->settings[ 'maxLenMark' ] + 1) . ',}#iu',
			'#(^|[^!])!{2}($|[^!])#iu',
			'#(^|[^?])\?{2}($|[^?])#iu',
		];

		$replace = [
			'$1',
			'$1',
			'$1$2',
			'$1$1$1',
			str_repeat('$1', $this->settings[ 'maxLenMark' ]),
			str_repeat('$1', $this->settings[ 'maxLenMark' ]),
			'$1!$2',
			'$1?$2',
		];

		return preg_replace($pattern, $replace, $text);
	}
}

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

class Fraction extends AbstractRule
{
    public string $name = 'Fraction';

    public bool $active = false;

    public function handler(string $text): string
    {
        $pattern = [
            '#(^|\D)1/([24])(\D|$)#iu',
            '#(^|\D)3/4(\D|$)#iu',
        ];

        $replace = [
            '$1&frac1$2;$3',
            '$1&frac34;$2',
        ];

        return preg_replace($pattern, $replace, $text);
    }
}

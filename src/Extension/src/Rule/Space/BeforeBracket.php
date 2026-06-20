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

class BeforeBracket extends AbstractRule
{
    public string $name = 'Before Bracket';

    public function handler(string $text): string
    {
        $pattern = '#(['.$this->char['char'].$this->char['charEnd'].'…)])\(#iu';

        $replace = '$1 (';

        return preg_replace($pattern, $replace, $text);
    }
}

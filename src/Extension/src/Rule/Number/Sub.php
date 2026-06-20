<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Number;

use JUTypography\Rule\AbstractRule;

class Sub extends AbstractRule
{
    public string $name = 'Sub _{n}';

    public function handler(string $text): string
    {
        $pattern = '#(['.$this->char['char'].'0-9])_{([^}]+)}([^@'.$this->char['char'].'0-9]|$)#iu';
        $replace = '$1<sub>$2</sub>$3';

        return preg_replace($pattern, $replace, $text);
    }
}

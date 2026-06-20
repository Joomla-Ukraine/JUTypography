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

class Sup extends AbstractRule
{
    public string $name = 'Sup ^';

    public function handler(string $text): string
    {
        $pattern = '#(['.$this->char['char'].'0-9])\^([\d]{1,3})([^'.$this->char['char'].'0-9]|$)#iu';
        $replace = '$1<sup>$2</sup>$3';

        return preg_replace($pattern, $replace, $text);
    }
}

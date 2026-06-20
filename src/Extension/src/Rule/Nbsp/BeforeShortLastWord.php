<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Nbsp;

use JUTypography\Rule\AbstractRule;

class BeforeShortLastWord extends AbstractRule
{
    public string $name = 'Before Short Last Word';

    protected array $settings = [
        'len' => 3,
    ];

    public function handler(string $text): string
    {
        $pattern = [
            '#(\S)\s(['.$this->char['char'].'\d]{1,'.$this->settings['len'].'}[.!?…])(\s['.$this->char['char'].']|<|$)#iu',
            '#(\S)\s(['.$this->char['char'].'\d]{1,'.$this->settings['len'].'})($|<)#iu',
        ];

        $replace = [
            '$1'.$this->char['nbsp'].'$2$3',
            '$1'.$this->char['nbsp'].'$2$3',
        ];

        return preg_replace($pattern, $replace, $text);
    }
}

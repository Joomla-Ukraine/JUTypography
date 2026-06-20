<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Dash;

use JUTypography\Rule\AbstractRule;

class ReplaceDash extends AbstractRule
{
    public string $name = 'Replace Dash';

    protected array $settings = [
        'len' => 2,
    ];

    protected int $sort = -100;

    public function handler(string $text): string
    {
        $pattern = '#(\s|&nbsp;)('.$this->char['allDash'].'){1,'.$this->settings['len'].'}(\s|&nbsp;)#iu';
        $replace = $this->char['nbsp'].$this->char['dash'].'$3';

        return preg_replace($pattern, $replace, $text);
    }
}

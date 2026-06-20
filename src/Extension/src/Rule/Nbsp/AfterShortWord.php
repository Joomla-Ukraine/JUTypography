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

class AfterShortWord extends AbstractRule
{
    public string $name = 'After Short Word';

    protected array $settings = [
        'len' => 2,
    ];

    public function handler(string $text): string
    {
        $before = '\s('.$this->char['allQuote'];
        $pattern = '#(^|'.$this->char['nbsp'].'|[a-z0-9];|['.$before.'])(['.$this->char['char'].']{1,'.$this->settings['len'].'})\s#iu';
        $replace = '$1$2'.$this->char['nbsp'];

        $text = preg_replace($pattern, $replace, $text);

        return preg_replace($pattern, $replace, $text);
    }
}

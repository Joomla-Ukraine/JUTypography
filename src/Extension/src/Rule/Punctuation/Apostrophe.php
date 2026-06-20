<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Punctuation;

use JUTypography\Rule\AbstractRule;

class Apostrophe extends AbstractRule
{
    public string $name = 'Apostrophe';

    public function handler(string $text): string
    {
        $pattern = '#(['.$this->char['char'].']+)\'(['.$this->char['char'].']+)#iu';

        $replace = '$1&rsquo;$2';

        return preg_replace($pattern, $replace, $text);
    }
}

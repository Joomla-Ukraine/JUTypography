<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Symbol;

use JUTypography\Rule\AbstractRule;

class Arrow extends AbstractRule
{
    public string $name = 'Arrow -> → →, <- → ←';

    public function handler(string $text): string
    {
        $pattern = [
            '#<<#iu',
            '#>>#iu',
            '#(^|[^-])->(?!>)#iu',
            '#(^|[^<])<-(?!-)#iu',
        ];

        $replace = [
            '&Lt;',
            '&Gt;',
            '$1&rarr;',
            '$1&larr;',
        ];

        return preg_replace($pattern, $replace, $text);
    }
}

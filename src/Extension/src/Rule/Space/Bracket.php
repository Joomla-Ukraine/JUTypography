<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Space;

use JUTypography\Rule\AbstractRule;

class Bracket extends AbstractRule
{
    public string $name = 'Bracket';

    public function handler(string $text): string
    {
        $pattern = [
            '#(\()\s+#u',
            '#\s+\)#u',
        ];

        $replace = [
            '(',
            ')',
        ];

        return preg_replace($pattern, $replace, $text);
    }
}

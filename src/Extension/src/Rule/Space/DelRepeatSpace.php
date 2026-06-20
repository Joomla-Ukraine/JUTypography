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

class DelRepeatSpace extends AbstractRule
{
    public string $name = 'Del Repeat Space';

    protected int $sort = -100;

    public function handler(string $text): string
    {
        $pattern = '#[ \t]{2,}#u';

        $replace = ' ';

        return preg_replace($pattern, $replace, $text);
    }
}

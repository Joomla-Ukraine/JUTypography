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

class Triad extends AbstractRule
{
    public string $name = 'Triad';

    public int $sort = 800;

    public function handler(string $text): string
    {
        return preg_replace_callback('#(^| |>|&nbsp;)([0-9]{5,})( |<|&nbsp;|$)#mu', static function ($matches) {
            $num = str_replace(' ', '&thinsp;', number_format((int)$matches[2], 0, '', ' '));

            return $matches[1].$num.$matches[3];
        }, $text);
    }
}

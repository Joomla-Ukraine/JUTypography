<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Punctuation;

use JUTypography\Rule\AbstractRule;

class Hellip extends AbstractRule
{
    public string $name = 'Hellip';

    protected int $sort = 800;

    public function handler(string $text): string
    {
        $pattern = '#(\.\.\.)#iu';

        $replace = '&hellip;';

        return preg_replace($pattern, $replace, $text);
    }
}

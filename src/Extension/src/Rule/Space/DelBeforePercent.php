<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025 Denes Nosov.
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Space;

use JUTypography\Rule\AbstractRule;

class DelBeforePercent extends AbstractRule
{
    public string $name = 'Del Before Percent';

    protected int $sort = 300;

    public function handler(string $text): string
    {
        $pattern = '#(\d)(\s|&nbsp;)([%‰‱])#iu';

        $replace = '$1$3';

        return preg_replace($pattern, $replace, $text);
    }
}

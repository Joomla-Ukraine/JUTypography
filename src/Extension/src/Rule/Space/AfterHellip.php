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

class AfterHellip extends AbstractRule
{
    public string $name = 'After Hellip';

    protected int $sort = 800;

    public function handler(string $text): string
    {
        $pattern = [
            '#(['.$this->char['char'].'])(\.\.\.|…)([А-ЯЁ])#u',
            '#([?!]\.\.)(['.$this->char['char'].']|$)#iu',
        ];

        $replace = [
            '$1$2 $3',
            '$1 $2',
        ];

        return preg_replace($pattern, $replace, $text);
    }
}

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

class ReplaceNbsp extends AbstractRule
{
    public string $name = 'Replace Nbsp';

    protected int $sort = -100;

    protected bool $active = false;

    public function handler(string $text): string
    {
        return str_replace($this->char['nbsp'], ' ', $text);
    }
}

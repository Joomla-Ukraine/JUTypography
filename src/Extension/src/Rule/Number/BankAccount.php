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

class BankAccount extends AbstractRule
{
    public string $name = 'Bank Account';

    public function handler(string $text): string
    {
        return preg_replace_callback('#(^| |>|&nbsp;)([0-9]{20})( |<|&nbsp;|$)#mu', static function ($matches) {
            $arNum = [];
            $arNum[] = substr($matches[2], 0, 5);
            $arNum[] = substr($matches[2], 5, 3);
            $arNum[] = substr($matches[2], 8, 1);
            $arNum[] = substr($matches[2], 9, 4);
            $arNum[] = substr($matches[2], 13, 7);

            return $matches[1].implode('&thinsp;', $arNum).$matches[3];
        }, $text);
    }
}

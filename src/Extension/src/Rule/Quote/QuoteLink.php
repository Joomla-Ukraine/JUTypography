<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography\Rule\Quote;

use JUTypography\Rule\AbstractRule;

class QuoteLink extends AbstractRule
{
    public string $name = 'Quote Link <a>';

    public function handler(string $text): string
    {
        $pattern = '#(<a[^>]+>)(['.$this->char['allQuote'].'])([\s\S]*?)(['.$this->char['allQuote'].'])(</a>)#iu';

        $replace = '$2$1$3$5$4';

        return preg_replace($pattern, $replace, $text);
    }
}

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

class Address extends AbstractRule
{
    public string $name = 'Неразрывной пробел а адресах г. ул. кв.';

    protected int $sort = 450;

    public function handler(string $text): string
    {
        $pattern = [
            '#(^|\s|>)(г|обл|р-н|вул|пров|пер|пр|просп|пл|наб|ш|туп|оф|к|комн?|буд|корп|кв|пов|эт|мкр|стр)\.\s+(\S)#iu',
            '#(^|\s|>)(будинок|корпус|квартира|поверх|офіс)\s+(\S)#iu',
        ];

        $replace = [
            '$1$2.'.$this->char['nbsp'].'$3',
            '$1$2'.$this->char['nbsp'].'$3',
        ];

        return preg_replace($pattern, $replace, $text);
    }
}

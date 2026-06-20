<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

namespace JUTypography;

use JUTypography\Rule\AbstractRule;

/**
 * @internal
 */
class Debug
{
    public const STATUS_DEACTIVATE = 'deactivate';

    public const STATUS_MODIFY = 'modify';

    public const STATUS_NOT_MODIFY = 'not modify';

    /**
     * @var string
     */
    private $startValue = '';

    /**
     * @var string
     */
    private $endValue = '';

    /**
     * @var array<array<string, string>>
     */
    private $trace = [];

    public function __construct()
    {
    }

    /**
     * @param string $startValue
     */
    public function setStartValue(string $startValue): void
    {
        $this->startValue = $startValue;
    }

    /**
     * @param string $endValue
     */
    public function setEndValue(string $endValue): void
    {
        $this->endValue = $endValue;
    }

    /**
     * @param AbstractRule $rule
     * @param string $start
     * @param string $end
     * @param string $status
     */
    public function addTrace(
        Rule\AbstractRule $rule,
        string $start,
        string $end,
        string $status
    ): void {
        $this->trace[] = [
            'rule' => get_class($rule),
            'startText' => $start,
            'endText' => $end,
            'status' => $status,
        ];
    }

    /**
     * @return array<string, array<array<string, string>>|string>
     */
    public function getAllInfo(): array
    {
        return $this->getInfo($this->trace);
    }

    /**
     * @return array<string, array<array<string, string>>|string>
     */
    public function getModifyInfo(): array
    {
        $trace = [];
        foreach ($this->trace as $item) {
            if (self::STATUS_MODIFY === $item['status']) {
                $trace[] = $item;
            }
        }

        return $this->getInfo($trace);
    }

    /**
     * @param array<array<string, string>> $trace
     *
     * @return array<string, array<array<string, string>>|string>
     */
    private function getInfo(array $trace): array
    {
        return [
            'start' => $this->startValue,
            'end' => $this->endValue,
            'trace' => $trace,
        ];
    }
}

<?php
/**
 * @package     JU.Plugin
 * @subpackage  Content.JUTypography
 *
 * @copyright   Copyright (C) 2025-2026 Denys Nosov
 * @license     GNU General Public License version 3 or later.
 */

defined('_JEXEC') or die;

class plgContentJUTypographyInstallerScript
{
    /**
     * @var
     */
    protected $message;

    /**
     * @var
     */
    protected $status;

    /**
     * @param $type
     * @param $parent
     *
     * @return bool
     * @throws \Exception
     */
    public function preflight($type, $parent): bool
    {
        $path = JPATH_SITE.'/plugins/content/jutypography/';

        $folders = [
            $path.'src/Extension/vendor',
        ];

        foreach ($folders as $folder) {
            if (is_dir($folder)) {
                $this->unlinkRecursive($folder);
            }
        }

        return true;
    }

    /**
     * @param $type
     * @param $parent
     *
     * @return bool
     */
    public function postflight($type, $parent): bool
    {
        return true;
    }

    /**
     * @param $dir
     * @param $deleteRootToo
     */
    private function unlinkRecursive($dir, $deleteRootToo = 1): void
    {
        if (!$dh = opendir($dir)) {
            return;
        }

        while (($obj = readdir($dh)) !== false) {
            if ($obj === '.' || $obj === '..') {
                continue;
            }

            if (!unlink($dir.'/'.$obj)) {
                $this->unlinkRecursive($dir.'/'.$obj, true);
            }
        }

        closedir($dh);

        if ($deleteRootToo) {
            rmdir($dir);
        }
    }
}
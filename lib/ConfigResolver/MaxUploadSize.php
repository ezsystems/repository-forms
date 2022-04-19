<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\ConfigResolver;

class MaxUploadSize
{
    const BYTES = 'B';
    const KILOBYTES = 'K';
    const MEGABYTES = 'M';
    const GIGABYTES = 'G';

    /** @var int */
    protected $value;

    /**
     * Return value of upload_max_filesize in bytes.
     */
    public function get(?string $unit = null): int
    {
        if (null === $this->value) {
            $this->value = $this->stringToBytes(ini_get('upload_max_filesize'));
        }
        $value = $this->value;

        switch ($unit) {
            case self::GIGABYTES:
                $value /= 1024;
            case self::MEGABYTES:
                $value /= 1024;
            case self::KILOBYTES:
                $value /= 1024;
            case self::BYTES:
            default:
        }

        return $value;
    }

    protected function stringToBytes(string $str): int
    {
        $str = strtoupper(trim($str));

        $value = substr($str, 0, -1);
        $unit = substr($str, -1);
        switch ($unit) {
            case self::GIGABYTES:
                $value *= 1024;
            case self::MEGABYTES:
                $value *= 1024;
            case self::KILOBYTES:
                $value *= 1024;
        }

        return (int) $value;
    }
}

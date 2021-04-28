<?php

namespace Codememory\FileSystem;

use Codememory\Components\UnitConversion\Conversion;
use Codememory\Components\UnitConversion\Units\AbstractUnit;
use Codememory\Components\UnitConversion\Units\FromBytes;
use Codememory\FileSystem\Interfaces\FileInterface;

/**
 * Class Information
 * @package Codememory\FileSystem\src
 *
 * @author  Codememory
 */
class Information
{

    /**
     * @var FileInterface
     */
    private FileInterface $file;

    /**
     * @var int|float
     */
    private int|float $size = 0;

    /**
     * @var AbstractUnit
     */
    private AbstractUnit $conversion;

    /**
     * Information constructor.
     *
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {

        $conversion = new Conversion();

        $this->file = $file;
        $this->conversion = $conversion->from(new FromBytes());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Obtaining the file size in different units by default in bytes
     * & To retrieve on another system, use the UnitsInterface
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param string $unit
     * @param bool   $recursion
     *
     * @return int|float
     */
    public function getSize(string $path, string $unit = 'getConvertible', bool $recursion = false): int|float
    {

        $conversion = $this->conversion->setConvertibleNumber(
            $this->getSizeHandler($path, $recursion)
        );

        $this->size = 0;

        return call_user_func_array([$conversion, $unit], []);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns the time the file was last modified
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $path
     * @param string|null $format
     *
     * @return int|string
     */
    public function lastModified(string $path, ?string $format = null): int|string
    {

        $modify = filectime($this->file->getRealPath($path));

        if(null === $format) {
            return (int) $modify;
        }

        return date($format, $modify);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns full information about a file or directory
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param $path
     *
     * @return array
     */
    public function getAllInfo($path): array
    {

        $info = [];

        if (is_string($path)) {
            $info = $this->getArrAllInfo($path);

            if ($this->file->is->file($path)) {
                $info['extension'] = $this->file->extension($path);
                $info['size'] = $this->getArrayReadyMeasurementsUnits($path);
            } elseif ($this->file->is->directory($path)) {
                $info['size'] = $this->getArrayReadyMeasurementsUnits($path, true);
            }
        }

        return $info;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Handler for getting the size if the specified file
     * or directory
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param bool   $recursion
     *
     * @return int|float
     */
    private function getSizeHandler(string $path, bool $recursion = false): int|float
    {

        if ($this->file->exist($path)) {
            if (false === $recursion) {
                return filesize($this->file->getRealPath($path));
            }
            $this->sizeCalculation($path);

            return $this->size;
        }

        return 0;

    }

    /**
     * @param string $dir
     *
     * @return void
     */
    private function sizeCalculation(string $dir): void
    {

        $dir = rtrim($dir, '/') . '/';

        if($this->file->is->directory($dir)) {
            $attached = $this->file->scanning($dir);
        } else {
            $attached = [];
        }

        if ([] !== $attached) {
            foreach ($attached as $path) {
                $path = $dir . $path;

                if ($this->file->is->file($path)) {
                    $this->size += $this->getSizeHandler($path);
                } else {
                    $this->size += $this->getSizeHandler($path);
                    $this->sizeCalculation($path);
                }
            }
        } else {
            $this->size += $this->getSizeHandler($dir);
        }

    }

    /**
     * @param $path
     *
     * @return array
     */
    private function getArrAllInfo($path): array
    {

        $realPath = $this->file->getRealPath($path);

        return [
            'type'         => $this->file->type($path),
            'lastModified' => $this->lastModified($path),
            'path'         => $this->file->dirname($path),
            'group'        => posix_getpwuid(filegroup($realPath))['name'],
            'owner'        => posix_getpwuid(fileowner($realPath))['name'],
            'permissions'  => substr(sprintf('%o', fileperms($realPath)), -4)
        ];

    }

    /**
     * @param      $path
     * @param bool $recursion
     *
     * @return array
     */
    private function getArrayReadyMeasurementsUnits($path, bool $recursion = false): array
    {

        return [
            'b'  => $this->getSize($path, Conversion::CURRENT, $recursion),
            'kb' => $this->getSize($path, Conversion::UNIT_KB, $recursion),
            'mb' => $this->getSize($path, Conversion::UNIT_MB, $recursion),
            'gb' => $this->getSize($path, Conversion::UNIT_GB, $recursion),
        ];

    }

}
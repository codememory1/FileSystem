<?php

namespace Codememory\FileSystem;

use Codememory\FileSystem\Exceptions\FileNotFoundException;
use Codememory\FileSystem\Interfaces\FileInterface;
use JetBrains\PhpStorm\Pure;

/**
 * Class File
 * @package Codememory\FileSystem\src
 *
 * @author  Codememory
 */
class File implements FileInterface
{

    /**
     * @var Considered
     */
    public Considered $is;

    /**
     * @var Reader
     */
    public Reader $reader;

    /**
     * @var Writer
     */
    public Writer $writer;

    /**
     * @var Information
     */
    public Information $info;

    /**
     * File constructor.
     */
    public function __construct()
    {

        $this->init();

    }

    /**
     * @return void
     */
    private function init(): void
    {

        $this->is = new Considered($this);
        $this->reader = new Reader($this);
        $this->writer = new Writer($this);
        $this->info = new Information($this);

    }

    /**
     * {@inheritdoc}
     */
    public function getRealPath(?string $path = null): bool|string
    {

        $rootDirectory = dirname(__DIR__, 3);

        if (str_starts_with($path, '*')) {
            return substr(trim($path, '/'), 1);
        }

        return sprintf('%s/%s', $rootDirectory, trim($path, '/'));

    }

    /**
     * {@inheritdoc}
     */
    #[Pure] public function exist(string $path): bool
    {

        return file_exists($this->getRealPath($path));

    }

    /**
     * {@inheritdoc}
     */
    public function scanning(array|string $path, bool $recursion = false, array $ignoring = []): array
    {

        $path = is_string($path) ? [$path] : $path;
        $wholeResult = [];

        foreach ($path as $value) {
            if ($recursion) {
                $scanResult = $this->recursiveScan($value, $ignoring);
            } else {
                $scanResult = scandir($this->getRealPath($value));
                $scanResult = array_diff($scanResult, ['.', '..']);
            }

            $wholeResult = array_merge($wholeResult, $scanResult);
        }

        $this->ignoreInScanning($wholeResult, $ignoring);

        return $wholeResult;

    }

    /**
     * {@inheritdoc}
     */
    public function setPermission(string $path, int $permission = 0777, bool $recursion = true): bool
    {

        return $this->handlerSetPermissionsOrOwner($path, $permission, 'chmod', $recursion);

    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(string $path, string $owner, bool $recursion = true): bool
    {

        return $this->handlerSetPermissionsOrOwner($path, $owner, 'chown', $recursion);

    }

    /**
     * {@inheritdoc}
     */
    public function mkdir(array|string $dirname, int $permission = 0777, bool $recursion = false): bool
    {

        $dirname = is_string($dirname) ? [$dirname] : $dirname;

        foreach ($dirname as $dir) {
            mkdir($this->getRealPath($dir), $permission, $recursion);

            $this->setPermission($dir, $permission);
        }

        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $path, string $newName): bool
    {

        if ($this->exist($path)) {
            $cutPath = explode('/', $path);
            unset($cutPath[array_key_last($cutPath)]);

            $generateName = $this->getRealPath(implode('/', $cutPath) . '/' . $newName);

            return rename($this->getRealPath($path), $generateName);
        }

        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function remove(array|string $path, bool $recursion = false, bool $removeCurrentDir = false): bool
    {

        $fsToDelete = [];
        $path = is_array($path) ? $path : [$path];

        foreach ($path as $item) {
            $fsToDelete = array_merge($fsToDelete, $this->getFsToDelete($item, $recursion));

            if ($this->is->directory($item) && false === $removeCurrentDir) {
                array_pop($fsToDelete);
            }
        }

        return $this->removing($fsToDelete);

    }

    /**
     * {@inheritdoc}
     */
    public function move(string $path, string $moveTo): bool
    {

        if ($this->exist($path)) {
            $cutPath = explode('/', $path);
            $movePath = $cutPath[array_key_last($cutPath)];

            return rename($this->getRealPath($path), $this->getRealPath($moveTo . '/' . $movePath));
        }

        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(string $path, string|int $group): bool
    {

        if ($this->exist($path)) {
            return chgrp($this->getRealPath($path), $group);
        }

        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function basename(string $path, ?string $suffix = null): string
    {

        return $this->baseOrDir($path, 'array_pop', $suffix);

    }

    /**
     * {@inheritdoc}
     */
    public function createLink(string $target, string $linkName, bool $tough = false): bool
    {

        if ($tough) {
            return symlink($target, $linkName);
        }

        return link($target, $linkName);

    }

    /**
     * {@inheritdoc}
     */
    public function readLink(string $path): string|bool
    {

        if ($this->is->link($path)) {
            return readlink($path);
        }

        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function removeLink(string $path, mixed $context = null): bool
    {

        if ($this->is->link($path)) {
            return is_resource($context) ? unlink($path, $context) : unlink($path);
        }

        return false;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns the file extension
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return string
     */
    #[Pure] public function extension(string $path): string
    {

        return pathinfo($this->getRealPath($path), PATHINFO_EXTENSION);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns before the last part of the element of the
     * & string that is split into a symbol [/]
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $path
     * @param string|null $suffix
     *
     * @return string
     */
    public function dirname(string $path, ?string $suffix = null): string
    {

        return $this->baseOrDir($path, 'array_shift', $suffix);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns the type. Which will define a file or a
     * & directory or something else
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return string
     */
    #[Pure] public function type(string $path): string
    {

        return filetype($this->getRealPath($path));

    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     */
    public function getImport(string $path, array $parameters = []): mixed
    {

        return $this->importHandler($path, function (string $path, array $parameters) {
            $overrideParameters = $parameters;

            extract($overrideParameters, EXTR_SKIP);

            /** @noinspection PhpIncludeInspection */
            return require $path;
        }, $parameters);

    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     */
    public function singleImport(string $path, array $parameters = []): mixed
    {

        return $this->importHandler($path, function (string $path, array $parameters) {
            $overrideParameters = $parameters;

            extract($overrideParameters, EXTR_SKIP);

            /** @noinspection PhpIncludeInspection */
            return require_once $path;
        }, $parameters);

    }

    /**
     * @param string   $path
     * @param callable $handler
     * @param array    $parameters
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    private function importHandler(string $path, callable $handler, array $parameters = []): mixed
    {

        if (!$this->exist($path)) {
            throw new FileNotFoundException($path);
        }

        return call_user_func($handler, $path, $parameters);

    }

    /**
     * @param string $path
     * @param array  $ignoring
     *
     * @return bool|array
     */
    private function recursiveScan(string $path, array $ignoring = []): bool|array
    {

        $scan = $this->scanning($path, false, $ignoring);
        $resultScan = [];

        foreach ($scan as $value) {
            $pathWithDirectory = trim($path, '/') . '/' . $value;

            $resultScan[] = $pathWithDirectory;

            if ($this->is->directory($pathWithDirectory)) {
                $resultScan = array_merge($this->recursiveScan($pathWithDirectory, $ignoring), $resultScan);
            }
        }

        return $resultScan;

    }

    /**
     * @param array $wholeResult
     * @param array $ignoring
     *
     * @return void
     */
    private function ignoreInScanning(array &$wholeResult, array $ignoring = []): void
    {

        foreach ($wholeResult as $key => $value) {
            foreach ($ignoring as $item) {
                if (preg_match(
                    '/' . str_replace('/', '\/', $item) . '/',
                    $value
                )) {
                    unset($wholeResult[$key]);
                }
            }
        }

    }

    /**
     * @param string     $path
     * @param string|int $userOrPermission
     * @param string     $function
     * @param bool       $recursion
     *
     * @return bool
     */
    private function handlerSetPermissionsOrOwner(string $path, string|int $userOrPermission, string $function, bool $recursion): bool
    {

        if (!$recursion || $this->is->file($path)) {
            if ($this->exist($path)) {
                $function($this->getRealPath($path), $userOrPermission);
            }
        } else {
            if ($this->is->directory($path)) {
                foreach ($this->scanning($path, true) as $value) {
                    $function($this->getRealPath($value), $userOrPermission);
                }

                $function($this->getRealPath($path), $userOrPermission);
            }
        }

        return true;

    }

    /**
     * @param string $path
     * @param bool   $recursion
     *
     * @return array
     */
    private function getFsToDelete(string $path, bool $recursion = false): array
    {

        $delete = [];

        if ($this->is->file($path)) {
            $delete[] = $path;
        } else if ($this->is->directory($path)) {
            $fs = $this->scanning($path);

            foreach ($fs as $f) {
                $currentPath = sprintf('%s/%s', $path, $f);

                if ($recursion) {
                    $delete = array_merge($delete, $this->getFsToDelete($currentPath, $recursion));
                }
            }
            $delete[] = $path;
        }

        return $delete;

    }

    /**
     * @param array $fsToDelete
     *
     * @return bool
     */
    private function removing(array $fsToDelete): bool
    {

        foreach ($fsToDelete as $item) {
            if ($this->is->file($item)) {
                unlink($this->getRealPath($item));
            } else {
                rmdir($this->getRealPath($item));
            }
        }

        return true;

    }

    /**
     * @param string      $path
     * @param string      $receiveFunction
     * @param string|null $suffix
     *
     * @return string
     */
    private function baseOrDir(string $path, string $receiveFunction, ?string $suffix = null): string
    {

        $usuallyBreaks = explode('/', $path);
        $breaksForWindows = explode('\\', $receiveFunction($usuallyBreaks));

        $basename = $receiveFunction($breaksForWindows);

        if (null !== $suffix) {
            $basename = mb_substr($basename, 0, mb_stripos($basename, $suffix));
        }

        return $basename;

    }

}
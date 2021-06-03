<?php

namespace Codememory\FileSystem\Interfaces;

/**
 * Interface FileInterface
 * @package Codememory\FileSystem\Interfaces
 *
 * @author  Codememory
 */
interface FileInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method returns the full path to the root and with the
     * option to add the path
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $path
     *
     * @return bool|string
     */
    public function getRealPath(?string $path = null): bool|string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method returns boolean checks whether such file or
     * directory  exists in the specified path
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return bool
     */
    public function exist(string $path): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method returns an array with the result of scanning paths.
     * You can specify an array of paths to scan, and also ignore
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     * @param bool         $recursion
     * @param array        $ignoring
     *
     * @return array
     */
    public function scanning(array|string $path, bool $recursion = false, array $ignoring = []): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set|Change file or directory permissions and specifying
     * 3 will recurse
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param int    $permission
     * @param bool   $recursion
     *
     * @return bool
     */
    public function setPermission(string $path, int $permission = 0777, bool $recursion = true): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set|Change owner of file or folder 3 parameter recursive change
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param string $owner
     * @param bool   $recursion
     *
     * @return bool
     */
    public function setOwner(string $path, string $owner, bool $recursion = true): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method creates a folder or creates folders recursively
     * i.e. sub folders
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $dirname
     * @param int          $permission
     * @param bool         $recursion
     *
     * @return bool
     */
    public function mkdir(array|string $dirname, int $permission = 0777, bool $recursion = false): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Method renames a folder or file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param string $newName
     *
     * @return bool
     */
    public function rename(string $path, string $newName): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Deleting a file or folder 2 argument recursive deletion,
     * i.e. if there are sub folders and files in the folder and
     * you need to delete the entire folder then 2 argument must be true
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     * @param bool         $recursion
     * @param bool         $removeCurrentDir
     *
     * @return bool
     */
    public function remove(array|string $path, bool $recursion = false, bool $removeCurrentDir = false): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Method moves a file or folder to a new location
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     * @param string $moveTo
     *
     * @return bool
     */
    public function move(string $path, string $moveTo): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method adds or changes a group of a file or folder
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string     $path
     * @param string|int $group
     *
     * @return bool
     */
    public function setGroup(string $path, string|int $group): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the last component of the name from the specified path
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string      $path
     * @param string|null $suffix
     *
     * @return string
     */
    public function basename(string $path, ?string $suffix = null): string;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create a hard or symbolic link, by default a symbolic link
     * is created, but if you pass 3 arguments true, a hard link
     * will be created
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $target
     * @param string $linkName
     * @param bool   $tough
     *
     * @return bool
     */
    public function createLink(string $target, string $linkName, bool $tough = false): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Read link will return false in case of error
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $path
     *
     * @return string|bool
     */
    public function readLink(string $path): string|bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>
     * Delete file/link
     * <=<=<=<=<=<=<=<=<=<=
     *
     * @param string     $path
     * @param mixed|null $context
     *
     * @return bool
     */
    public function removeLink(string $path, mixed $context = null): bool;

    /**
     * @param string $path
     * @param array  $parameters
     *
     * @return mixed
     */
    public function singleImport(string $path, array $parameters = []): mixed;

    /**
     * @param string $path
     * @param array  $parameters
     *
     * @return mixed
     */
    public function getImport(string $path, array $parameters = []): mixed;

}
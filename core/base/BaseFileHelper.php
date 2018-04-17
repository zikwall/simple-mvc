<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 19.01.2018
 * Time: 18:32
 */

namespace core\base;


use core\Zii;
use Whoops\Exception\ErrorException;

class BaseFileHelper
{
    const PATTERN_NODIR = 1;
    const PATTERN_ENDSWITH = 4;
    const PATTERN_MUSTBEDIR = 8;
    const PATTERN_NEGATIVE = 16;
    const PATTERN_CASE_INSENSITIVE = 32;

    /**
     * @var string the path (or alias) of a PHP file containing MIME type information.
     */
    public static $mimeMagicFile = '@core/helpers/mimeTypes.php';

    /**
     * @var string the path (or alias) of a PHP file containing MIME aliases.
     */
    public static $mimeAliasesFile = '@core/helpers/mimeAliases.php';

    /**
     * @param string $path the file/directory path to be normalized
     * @param string $ds the directory separator to be used in the normalized result. Defaults to `DIRECTORY_SEPARATOR`.
     * @return string the normalized file/directory path
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
    {
        $path = rtrim(strtr($path, '/\\', $ds . $ds), $ds);
        if (strpos($ds . $path, "{$ds}.") === false && strpos($path, "{$ds}{$ds}") === false) {
            return $path;
        }
        // the path may contain ".", ".." or double slashes, need to clean them up
        $parts = [];
        foreach (explode($ds, $path) as $part) {
            if ($part === '..' && !empty($parts) && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part === '.' || $part === '' && !empty($parts)) {
                continue;
            } else {
                $parts[] = $part;
            }
        }
        $path = implode($ds, $parts);
        return $path === '' ? '.' : $path;
    }

    /**
     * @param $file
     * @param null $language
     * @param null $sourceLanguage
     * @return string
     */
    public static function localize($file, $language = null, $sourceLanguage = null)
    {
        if ($language === null) {
            $language = Zii::$app->language;
        }
        if ($sourceLanguage === null) {
            $sourceLanguage = Zii::$app->sourceLanguage;
        }
        if ($language === $sourceLanguage) {
            return $file;
        }
        $desiredFile = dirname($file) . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . basename($file);
        if (is_file($desiredFile)) {
            return $desiredFile;
        }
        $language = substr($language, 0, 2);
        if ($language === $sourceLanguage) {
            return $file;
        }
        $desiredFile = dirname($file) . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . basename($file);
        return is_file($desiredFile) ? $desiredFile : $file;
    }

    public static function getMimeType($file, $magicFile = null, $checkExtension = true)
    {
        if ($magicFile !== null) {
            $magicFile = Zii::getAlias($magicFile);
        }
        if (!extension_loaded('fileinfo')) {
            if ($checkExtension) {
                return static::getMimeTypeByExtension($file, $magicFile);
            }
            throw new InvalidConfigException('The fileinfo PHP extension is not installed.');
        }
        $info = finfo_open(FILEINFO_MIME_TYPE, $magicFile);
        if ($info) {
            $result = finfo_file($info, $file);
            finfo_close($info);
            if ($result !== false) {
                return $result;
            }
        }
        return $checkExtension ? static::getMimeTypeByExtension($file, $magicFile) : null;
    }

    /**
     * @param string $file the file name.
     * @param string $magicFile the path (or alias) of the file that contains all available MIME type information.
     * If this is not set, the file specified by [[mimeMagicFile]] will be used.
     * @return string|null the MIME type. Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeTypeByExtension($file, $magicFile = null)
    {
        $mimeTypes = static::loadMimeTypes($magicFile);
        if (($ext = pathinfo($file, PATHINFO_EXTENSION)) !== '') {
            $ext = strtolower($ext);
            if (isset($mimeTypes[$ext])) {
                return $mimeTypes[$ext];
            }
        }
        return null;
    }

    public static function getExtensionsByMimeType($mimeType, $magicFile = null)
    {
        $aliases = static::loadMimeAliases(static::$mimeAliasesFile);
        if (isset($aliases[$mimeType])) {
            $mimeType = $aliases[$mimeType];
        }
        $mimeTypes = static::loadMimeTypes($magicFile);
        return array_keys($mimeTypes, mb_strtolower($mimeType, 'UTF-8'), true);
    }

    private static $_mimeTypes = [];

    protected static function loadMimeTypes($magicFile)
    {
        if ($magicFile === null) {
            $magicFile = static::$mimeMagicFile;
        }
        $magicFile = Zii::getAlias($magicFile);
        if (!isset(self::$_mimeTypes[$magicFile])) {
            self::$_mimeTypes[$magicFile] = require $magicFile;
        }
        return self::$_mimeTypes[$magicFile];
    }

    private static $_mimeAliases = [];

    protected static function loadMimeAliases($aliasesFile)
    {
        if ($aliasesFile === null) {
            $aliasesFile = static::$mimeAliasesFile;
        }
        $aliasesFile = Zii::getAlias($aliasesFile);
        if (!isset(self::$_mimeAliases[$aliasesFile])) {
            self::$_mimeAliases[$aliasesFile] = require $aliasesFile;
        }
        return self::$_mimeAliases[$aliasesFile];
    }

    public static function copyDirectory($src, $dst, $options = [])
    {
        $src = static::normalizePath($src);
        $dst = static::normalizePath($dst);
        if ($src === $dst || strpos($dst, $src . DIRECTORY_SEPARATOR) === 0) {
            throw new InvalidParamException('Trying to copy a directory to itself or a subdirectory.');
        }
        $dstExists = is_dir($dst);
        if (!$dstExists && (!isset($options['copyEmptyDirectories']) || $options['copyEmptyDirectories'])) {
            static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
            $dstExists = true;
        }
        $handle = opendir($src);
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: $src");
        }
        if (!isset($options['basePath'])) {
            // this should be done only once
            $options['basePath'] = realpath($src);
            $options = static::normalizeOptions($options);
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $from = $src . DIRECTORY_SEPARATOR . $file;
            $to = $dst . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($from, $options)) {
                if (isset($options['beforeCopy']) && !call_user_func($options['beforeCopy'], $from, $to)) {
                    continue;
                }
                if (is_file($from)) {
                    if (!$dstExists) {
                        // delay creation of destination directory until the first file is copied to avoid creating empty directories
                        static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
                        $dstExists = true;
                    }
                    copy($from, $to);
                    if (isset($options['fileMode'])) {
                        @chmod($to, $options['fileMode']);
                    }
                } else {
                    // recursive copy, defaults to true
                    if (!isset($options['recursive']) || $options['recursive']) {
                        static::copyDirectory($from, $to, $options);
                    }
                }
                if (isset($options['afterCopy'])) {
                    call_user_func($options['afterCopy'], $from, $to);
                }
            }
        }
        closedir($handle);
    }

    public static function removeDirectory($dir, $options = [])
    {
        if (!is_dir($dir)) {
            return;
        }
        if (isset($options['traverseSymlinks']) && $options['traverseSymlinks'] || !is_link($dir)) {
            if (!($handle = opendir($dir))) {
                return;
            }
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    static::removeDirectory($path, $options);
                } else {
                    static::unlink($path);
                }
            }
            closedir($handle);
        }
        if (is_link($dir)) {
            static::unlink($dir);
        } else {
            rmdir($dir);
        }
    }

    public static function unlink($path)
    {
        $isWindows = DIRECTORY_SEPARATOR === '\\';
        if (!$isWindows) {
            return unlink($path);
        }
        if (is_link($path) && is_dir($path)) {
            return rmdir($path);
        }
        try {
            return unlink($path);
        } catch (ErrorException $e) {
            // last resort measure for Windows
            $lines = [];
            exec("DEL /F/Q \"$path\"", $lines, $deleteError);
            return $deleteError !== 0;
        }
    }

    public static function findFiles($dir, $options = [])
    {
        if (!is_dir($dir)) {
            throw new InvalidParamException("The dir argument must be a directory: $dir");
        }
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!isset($options['basePath'])) {
            // this should be done only once
            $options['basePath'] = realpath($dir);
            $options = static::normalizeOptions($options);
        }
        $list = [];
        $handle = opendir($dir);
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: $dir");
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($path, $options)) {
                if (is_file($path)) {
                    $list[] = $path;
                } elseif (is_dir($path) && (!isset($options['recursive']) || $options['recursive'])) {
                    $list = array_merge($list, static::findFiles($path, $options));
                }
            }
        }
        closedir($handle);
        return $list;
    }

    public static function filterPath($path, $options)
    {
        if (isset($options['filter'])) {
            $result = call_user_func($options['filter'], $path);
            if (is_bool($result)) {
                return $result;
            }
        }
        if (empty($options['except']) && empty($options['only'])) {
            return true;
        }
        $path = str_replace('\\', '/', $path);
        if (!empty($options['except'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['except'])) !== null) {
                return $except['flags'] & self::PATTERN_NEGATIVE;
            }
        }
        if (!empty($options['only']) && !is_dir($path)) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['only'])) !== null) {
                // don't check PATTERN_NEGATIVE since those entries are not prefixed with !
                return true;
            }
            return false;
        }
        return true;
    }

    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        // recurse if parent dir does not exist and we are not at the root of the file system.
        if ($recursive && !is_dir($parentDir) && $parentDir !== $path) {
            static::createDirectory($parentDir, $mode, true);
        }
        try {
            if (!mkdir($path, $mode)) {
                return false;
            }
        } catch (\Exception $e) {
            if (!is_dir($path)) {// https://github.com/yiisoft/yii2/issues/9288
                throw new ErrorException("Failed to create directory \"$path\": " . $e->getMessage(), $e->getCode(), $e);
            }
        }
        try {
            return chmod($path, $mode);
        } catch (\Exception $e) {
            throw new ErrorException("Failed to change permissions for directory \"$path\": " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    private static function lastExcludeMatchingFromList($basePath, $path, $excludes)
    {
        foreach (array_reverse($excludes) as $exclude) {
            if (is_string($exclude)) {
                $exclude = self::parseExcludePattern($exclude, false);
            }
            if (!isset($exclude['pattern']) || !isset($exclude['flags']) || !isset($exclude['firstWildcard'])) {
                throw new InvalidParamException('If exclude/include pattern is an array it must contain the pattern, flags and firstWildcard keys.');
            }
            if ($exclude['flags'] & self::PATTERN_MUSTBEDIR && !is_dir($path)) {
                continue;
            }
            if ($exclude['flags'] & self::PATTERN_NODIR) {
                if (self::matchBasename(basename($path), $exclude['pattern'], $exclude['firstWildcard'], $exclude['flags'])) {
                    return $exclude;
                }
                continue;
            }
            if (self::matchPathname($path, $basePath, $exclude['pattern'], $exclude['firstWildcard'], $exclude['flags'])) {
                return $exclude;
            }
        }
        return null;
    }

    protected static function normalizeOptions(array $options)
    {
        if (!array_key_exists('caseSensitive', $options)) {
            $options['caseSensitive'] = true;
        }
        if (isset($options['except'])) {
            foreach ($options['except'] as $key => $value) {
                if (is_string($value)) {
                    $options['except'][$key] = self::parseExcludePattern($value, $options['caseSensitive']);
                }
            }
        }
        if (isset($options['only'])) {
            foreach ($options['only'] as $key => $value) {
                if (is_string($value)) {
                    $options['only'][$key] = self::parseExcludePattern($value, $options['caseSensitive']);
                }
            }
        }
        return $options;
    }

}
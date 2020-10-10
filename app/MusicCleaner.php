<?php

namespace App;

use Illuminate\Support\Facades\File;

/**
 * Class MusicCleaner
 * @package App
 */
class MusicCleaner
{

    /**
     * @param bool $extensions
     * @param bool $filenameMatch
     * @return mixed
     */
    public function listFiles($extensions = false, $filenameMatch = false)
    {
        $fileCollection = collect();
        $folderPath = $this->folderPath();
        return $this->searchDirectory($fileCollection, $folderPath, $extensions, $filenameMatch);
    }

    /**
     * @param $path
     * @return string
     */
    private function ensureTrailingSlash($path)
    {
        return rtrim($path, '/') . '/';
    }

    /**
     * @param $fileCollection
     * @param $folderPath
     * @param $extensions
     * @param $filenameMatch
     * @return mixed
     */
    private function searchDirectory(&$fileCollection, $folderPath, $extensions, $filenameMatch = false)
    {
        $globPath = $this->ensureTrailingSlash($folderPath) . '*';

        if (is_array($extensions) && !empty($extensions)) {
            if (count($extensions) > 1) {
                $globPathWithExt = $globPath . '.{' . implode(',', $extensions) . '}';
            } else {
                $globPathWithExt = $globPath . '.' . implode(',', $extensions);
            }
        }

        // Look for folders
        $files = $this->glob($globPath);
        $files->each(function ($filePath) use (&$fileCollection, $extensions, $filenameMatch) {
            if (File::isDirectory($filePath)) {
                $this->searchDirectory($fileCollection, $filePath, $extensions, $filenameMatch);
            } elseif (!isset($globPathWithExt) && File::isFile($filePath)) {
                if ($this->shouldIncludeFile($filePath, $filenameMatch)) {
                    $fileCollection->add($filePath);
                }
            }
        });

        // Look for files using file extension filtering
        if (isset($globPathWithExt)) {
            $filesWithExt = File::glob($globPathWithExt);
            $filesWithExt->each(function ($filePath) use (&$fileCollection, $filenameMatch) {
                if (File::isFile($filePath) && $this->shouldIncludeFile($filePath, $filenameMatch)) {
                    $fileCollection->add($filePath);
                }
            });
        }
        return $fileCollection;
    }

    /**
     * @param $path
     * @return bool|\Illuminate\Support\Collection
     */
    private function glob($path)
    {
        $glob = File::glob($path);
        return is_array($glob) ? collect($glob) : false;
    }

    private function cleanFilename($filePath)
    {
        $pathParts = pathinfo($filePath);
        $filename = $pathParts['filename'];
        foreach( $this->stringsToClean() as $string ) {
            $filename = str_replace($string, '', $filename);
        }
        $newFilePath = $this->ensureTrailingSlash($pathParts['dirname']) . trim($filename) . '.' . $pathParts['extension'];
        return rename($filePath, $newFilePath);

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function cleanFilenames()
    {
        $filesToClean = $this->listFiles(false, $this->stringsToClean());
        $cleanedFiles = collect();
        $filesToClean->each(function ($fileToClean) use (&$cleanedFiles) {
            if ( $this->cleanFilename($fileToClean) ) {
                $cleanedFiles->add($fileToClean);
            }
        });
        return $cleanedFiles;
    }

    /**
     * @return array
     */
    public function stringsToClean()
    {
        $strings = explode(',', config('musicfolder.string_to_clean'));
        $strings = array_map('trim', $strings);
        $strings = array_filter($strings, function ($string) {
            return ! empty($string);
        });
        return $strings;
    }

    /**
     * @param string $filePath
     * @param array|string $filenameMatch
     * @return bool
     */
    private function shouldIncludeFile($filePath, $filenameMatch)
    {
        if ( ! $filenameMatch ) {
            return true;
        }
        if ( ! is_array($filenameMatch) ) {
            $filenameMatch = [$filenameMatch];
        }
        $pathParts = pathinfo($filePath);
        foreach($filenameMatch as $match) {
            if ( strpos($pathParts['filename'], $match) !== false ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getExtensions()
    {
        $files = $this->listFiles();
        $fileExtensions = collect();
        $files->each(function ($file) use (&$fileExtensions) {
            $fileExtension = File::extension($file);
            if ( ! $fileExtensions->contains($fileExtension) ) {
                $fileExtensions->add($fileExtension);
            }
        });
        return $fileExtensions;
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function folderPath()
    {

        return config('musicfolder.folder_path');
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function fileExtensions()
    {
        return config('musicfolder.file_extensions');
    }

}

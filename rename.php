<?php


define('FOLDER', './files/silvester');
define('PREFIX', 'IMG_');
define('RENAMING', true);

if (!file_exists(FOLDER)) {
    die("Can't find file/folder.");
}

function renamePhotos($dir)
{
    $output = shell_exec('exiftool -k -FileIndex -j "' . $dir . '"');
    $files = json_decode($output, true);

    if ($files == NULL) {
        die('Unkown Error');
    }

    // Go through files
    foreach ($files as $file) {
        $source = $file['SourceFile'];

        if (file_exists($source)) {
            if (!isset($file['FileIndex'])) {
                echo 'Skipping ' . $source . '<br>';
                continue;
            }

            $ext = pathinfo($source, PATHINFO_EXTENSION);
            $newName = rtrim(FOLDER, '/') . '/'. PREFIX . str_pad($file['FileIndex'], 4, 0) . '.' . $ext;
            echo 'Renaming: ' . $file['SourceFile'] . ' to ' . $newName . "<br>";

            if (RENAMING) {
                rename($source, $newName);
            }
        }
    }
}

renamePhotos(FOLDER);

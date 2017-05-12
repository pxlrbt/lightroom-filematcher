<?php

// Settings section
define('META_FILE', 'Summary.txt');    // Path to LR Transporter file
define('FILES_DIR', 'files/silvester'); // Non-recursive. Only one level supported

define('MAX_SELECTION', 3);             // If the data matches more than 3 files go on.
ini_set('max_execution_time', 0);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>EXIF Matcher</title>
        <style media="screen">
            html {
                font-family: Verdana, sans-serif;
                font-size: 13px;
            }

            table {
                width: 100%;
                border: 1px solid #ccc;
            }

            tr:nth-child(2n) {
                background-color: #eee;
            }

            th, td {
                padding: .5em 1em;
                vertical-align: middle;
            }

            th {
                background-color: #ccc;
            }

            img {
                max-width: 150;
                max-height: 150px;
                vertical-align: middle;
            }

            .message {
                background-color: rgba(36, 125, 236, 0.3);
                padding: 1.5em;
                margin-bottom: 2em;
            }
        </style>
    </head>
    <body>

<?php

error_reporting(E_ALL^E_NOTICE);

if ($_POST['rename']) :
?>

<div class="message">

    <?php
        foreach ($_POST['file'] as $old => $new) {
            echo 'Renaming "' . $old . '" to "' . $new . '"' . '<br>';
            $dir = rtrim(FILES_DIR, '/') . '/';
            rename($dir . $old, $dir . $new);
        }
    ?>

</div>

<?php
    endif;
?>

<form method="post">
<table>
    <tr>
        <th>#</th>
        <th>Local Filename</th>
        <th>Lightroom Filename</th>
    </tr>

<?php

$mapping = [];
$meta = [];
$lines = file(META_FILE);

foreach ($lines as $line) {
    $data = explode("\t", rtrim($line));

    $fileName = $data[0];

    $meta['shutterspeed'][$data[1]][] = $fileName;
    $meta['aperture'][$data[2]][] = $fileName;
    $meta['iso'][$data[3]][] = $fileName;
    $meta['date'][$data[4]][] = $fileName;
}

$output = shell_exec('exiftool -k -j "' . FILES_DIR . '"');
$files = json_decode($output, true);


$i = 0;
foreach ($files as $file) :
    $i++;
    $currentFile = new stdClass;
    $currentFile->source = $file['SourceFile'];
    $currentFile->filename = $file['FileName'];
    $currentFile->shutterspeed = $file['ShutterSpeed'];
    $currentFile->aperture = 'f/' . $file['ApertureValue'];
    $currentFile->iso = 'ISO ' . $file['ISO'];
    $currentFile->date = $file['DateTimeOriginal'];

    $fileRating = [];

    $possibleFiles = $meta['date'][$currentFile->date];
    if (count($possibleFiles) > 0) {
        foreach ($possibleFiles as $fileName) {
            $fileRating[$fileName] += 1;
        }
    }
    
    $possibleFiles = $meta['shutterspeed'][$currentFile->shutterspeed];
    if (count($possibleFiles) > 0) {
        foreach ($possibleFiles as $fileName) {
            $fileRating[$fileName] += 1;
        }
    }

    $possibleFiles = $meta['aperture'][$currentFile->aperture];
    if (count($possibleFiles) > 0) {
        foreach ($possibleFiles as $fileName) {
            $fileRating[$fileName] += 1;
        }
    }

    $possibleFiles = $meta['iso'][$currentFile->iso];
    if (count($possibleFiles) > 0) {
        foreach ($possibleFiles as $fileName) {
            $fileRating[$fileName] += 1;
        }
    }

    $possibleFiles = $meta['date'][$currentFile->date];
    if (count($possibleFiles) > 0) {
        foreach ($possibleFiles as $fileName) {
            $fileRating[$fileName] += 1;
        }
    }


    $max = @max($fileRating);
    $selection = array_keys($fileRating, $max);

    if (count($selection) == 0 || $max == 0): ?>
        <tr>
            <td>
                <?=$i;?>
            </td>
            <td>
                <?=$currentFile->filename; ?>
            </td>
            <td>
                <strong>No matching LR file found.</strong>
            </td>
        </tr>
    <?php else: ?>
        <tr>
            <td>
                <?=$i; ?>
            </td>
            <td>
                <?php
                    if (count($selection) > 1) {
                        $json = json_decode(shell_exec('exiftool -b -j -ThumbnailImage "' . $currentFile->source . '"'));

                        $currentFile->thumbnail = str_replace('base64:', 'base64,', $json[0]->ThumbnailImage);
                        echo '<img src="data:image/jpg;' . $currentFile->thumbnail . '"> ';
                    }

                    echo $currentFile->filename;
                ?>
            </td>
            <td>
                <?php
                    foreach ($selection as $name) {
                        echo '<input type="radio" name="file[' . $currentFile->filename . ']" value="' . $name . '" checked> ' . $name . '<br>';
                    }
                ?>
            </td>
        </tr>
    <?php endif; ?>
<?php endforeach; ?>

    </table>
    <input type="submit" name="rename" value="Rename">
<form>
</body>
</html>

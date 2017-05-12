# Lightroom Filematcher

I wrote these scripts for personal use but maybe they are helpful for other people too.
You need a local running server with php for these scripts.

## Introduction
When files are lost and recovered by software like "PhotoRec" the filenames are not restored. Therefore Lightroom can't link the files with them in the catalogue. These scripts help to link the files with Lightroom again.

## rename.php
This script renames your according to the File Index in the Exif data. If your camera stores the file index inside the Exif data this is the easiest way to go. Just adjust the path to the folder containing your files and your file prefix.

## match.php
This script tries to match the files in the specified folder with the meta data from Lightroom. To extract the exif it uses the tool [exiftool](http://www.sno.phy.queensu.ca/~phil/exiftool/). It compares aperture, shutterspeed, iso and date. If there are multiple files to choose from, it lets you select them before renaming supporting you with a thumbnail of the photo.

To retrieve the needed meta info you need the [LR Transporter Plugin](http://www.photographers-toolbox.com/products/lrtransporter.php). Set the settings to "Shared info file" and use the pattern from the "lr-transporter-rules.txt" file. Don't forget the new line at the end.

![Screenshot](https://raw.githubusercontent.com/pixelarbeit/lightroom-filematcher/master/screen.gif)

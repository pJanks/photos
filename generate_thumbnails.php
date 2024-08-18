<?php
    ini_set("memory_limit", "10G");
    ini_set("max_execution_time", 300);

    function createThumb($imagePath, $thumbPath, $extension) {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($imagePath);
            break;
            case 'png':
                $img = imagecreatefrompng($imagePath);
            break;
            case 'gif':
                $img = imagecreatefromgif($imagePath);
            break;
            default:
                return false;
            break;
        }

        $width = imagesx($img);
        $height = imagesy($img);
        $thumbWidth = 500;
        $thumbHeight = floor($height * ($thumbWidth / $width));
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
        imagejpeg($thumb, $thumbPath, 100);
        imagedestroy($img);
        imagedestroy($thumb);

        return true;
    }

    $thumbsDir = __DIR__ . "/thumbnails";
    $photosDir = __DIR__ . "/photos";
    $files = scandir($photosDir);
    $html = "";

    foreach ($files as $file) {
        $filePath = "$photosDir/$file";
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (is_file($filePath) && in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $thumbPath = "$thumbsDir/$file";

            if (!is_dir($thumbsDir)) {
                mkdir($thumbsDir, 0755, true);
            }
            
            if (!file_exists($thumbPath)) {
                if (!createThumb($filePath, $thumbPath, $extension)) continue;
            }
            
            $relativeThumbPath = str_replace(__DIR__, '', $thumbPath);
            $relativeFilePath = str_replace(__DIR__, '', $filePath);
            $filename = pathinfo($file, PATHINFO_FILENAME);

            $html .= "
            <div class=\"card\">
                <a href=\"$relativeFilePath\" download>
                    <img src=\"$relativeThumbPath\" alt=\"$filename\" loading=\"lazy\">
                </a>
                <span class=\"card-title\">$filename</span>
            </div>";
        }
    }
    echo "$html\n\t\t";

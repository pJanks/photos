<?php
    ini_set("memory_limit", "10G");
    ini_set("max_execution_time", 300);

    $thumbnailsDirectory = __DIR__ . "/thumbnails";
    $photosDirectory = __DIR__ . "/photos";
    $files = scandir($photosDirectory);
    $html = "";

    function createThumbnail($imagePath, $thumbnailPath, $extension) {
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
        imagejpeg($thumb, $thumbnailPath);
        imagedestroy($img);
        imagedestroy($thumb);
        return true;
    }

    foreach ($files as $file) {
        $filePath = "$photosDirectory/$file";
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (is_file($filePath) && in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $thumbnailPath = "$thumbnailsDirectory/$file";
            $thumbDir = dirname($thumbnailPath);

            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            
            if (!file_exists($thumbnailPath)) {
                if (!createThumbnail($filePath, $thumbnailPath, $extension)) continue;
            }
            
            $relativeThumbPath = str_replace(__DIR__, '', $thumbnailPath);
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

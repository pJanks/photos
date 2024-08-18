<?php
    ini_set("memory_limit", "10G");
    ini_set("max_execution_time", 300);

    $thumbnailsDirectory = __DIR__ . "/thumbnails";
    $photosDirectory = __DIR__ . "/photos";
    $files = scandir($photosDirectory);
    $thumbWidth = 500;
    $html = "";

    function createThumbnail($imagePath, $thumbnailPath, $thumbWidth) {
        $info = pathinfo($imagePath);
        $ext = strtolower($info["extension"]);

        if ($ext === "jpg" || $ext === "jpeg") {
            $img = imagecreatefromjpeg($imagePath);
        } else if ($ext === "png") {
            $img = imagecreatefrompng($imagePath);
        } else if ($ext === "gif") {
            $img = imagecreatefromgif($imagePath);
        } else {
            return false;
        }

        $width = imagesx($img);
        $height = imagesy($img);
        $thumbHeight = floor($height * ($thumbWidth / $width));
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
        imagejpeg($thumb, $thumbnailPath);
        imagedestroy($img);
        imagedestroy($thumb);
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
                createThumbnail($filePath, $thumbnailPath, $thumbWidth);
            }

            $relativeThumbnailPath = htmlspecialchars(str_replace(__DIR__, "", $thumbnailPath));
            $relativeFilePath = htmlspecialchars(str_replace(__DIR__, "", $filePath));
            $filename = htmlspecialchars(pathinfo($file, PATHINFO_FILENAME));
            $sanitizedImage = htmlspecialchars($file);

            $html .= "<div class=\"card\">";
            $html .= "<a href=\"$relativeFilePath\" download>";
            $html .= "<img src=\"$relativeThumbnailPath\" alt=\"$sanitizedImage\" loading=\"lazy\">";
            $html .= "</a>";
            $html .= "<span class=\"card-title\">$filename</span>";
            $html .= "</div>";
        }
    }

    echo $html;
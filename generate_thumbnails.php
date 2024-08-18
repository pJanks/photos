<?php
    ini_set("memory_limit", "10G");
    ini_set("max_execution_time", 300);

    $thumbnailsDirectory = __DIR__ . "/thumbnails";
    $photosDirectory = __DIR__ . "/photos";
    $thumbWidth = 500;
    $html = "";

    if (!is_dir($thumbnailsDirectory)) {
        mkdir($thumbnailsDirectory, 0755, true);
    }

    function createThumbnail($imagePath, $thumbnailPath, $thumbWidth) {
        $info = pathinfo($imagePath);
        $ext = strtolower($info["extension"]);

        switch ($ext) {
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
        }

        $width = imagesx($img);
        $height = imagesy($img);
        $thumbHeight = floor($height * ($thumbWidth / $width));
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
        imagejpeg($thumb, $thumbnailPath);
        imagedestroy($img);
        imagedestroy($thumb);
        return true;
    }

    $files = scandir($photosDirectory);
    foreach ($files as $file) {
        $filePath = "$photosDirectory/$file";
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (is_file($filePath) && in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $thumbnailPath = "$thumbnailsDirectory/$file";
            
            if (!file_exists($thumbnailPath) && !createThumbnail($filePath, $thumbnailPath, $thumbWidth)) continue;

            $relativeThumbnailPath = str_replace(__DIR__, '', $thumbnailPath);
            $relativeFilePath = str_replace(__DIR__, '', $filePath);        $relativeThumbnailPath = ltrim($relativeThumbnailPath, '/');
            $relativeFilePath = ltrim($relativeFilePath, '/');
            $filename = htmlspecialchars(pathinfo($file, PATHINFO_FILENAME));

            $html .= "<div class=\"card\">";
            $html .= "<a href=\"$relativeFilePath\" download>";
            $html .= "<img src=\"$relativeThumbnailPath\" alt=\"$filename\" loading=\"lazy\">";
            $html .= "</a>";
            $html .= "<span class=\"card-title\">$filename</span>";
            $html .= "</div>";
        }
    }
    echo $html;
?>

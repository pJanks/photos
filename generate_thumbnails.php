<?php
    ini_set("memory_limit", "10G");
    ini_set("max_execution_time", 300);

    $photosDirectory = __DIR__ . "/photos";
    $thumbnailsDirectory = __DIR__ . "/thumbnails";
    $thumbWidth = 500;

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

    $images = scandir($photosDirectory);
    $html = "";

    foreach ($images as $image) {
        $imagePath = "$photosDirectory/$image";
        $thumbnailPath = "$thumbnailsDirectory/$image";
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            continue;
        }

        if (is_file($imagePath)) {
            $thumbDir = dirname($thumbnailPath);
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }

            if (!file_exists($thumbnailPath)) {
                createThumbnail($imagePath, $thumbnailPath, $thumbWidth);
            }

            $relativeThumbnailPath = htmlspecialchars(str_replace(__DIR__, "", $thumbnailPath));
            $relativeImagePath = htmlspecialchars(str_replace(__DIR__, "", $imagePath));
            $filename = htmlspecialchars(pathinfo($image, PATHINFO_FILENAME));
            $sanitizedImage = htmlspecialchars($image);

            $html .= "<div class=\"card\">";
            $html .= "<a href=\"$relativeImagePath\" download>";
            $html .= "<img src=\"$relativeThumbnailPath\" alt=\"$sanitizedImage\" loading=\"lazy\">";
            $html .= "</a>";
            $html .= "<span class=\"card-title\">$filename</span>";
            $html .= "</div>";
        }
    }

    echo $html;
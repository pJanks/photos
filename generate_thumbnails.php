<?php
    ini_set("memory_limit", "10G");
    ini_set("max_execution_time", 300);

    $photosDirectory = __DIR__ . "/photos";
    $files = scandir($photosDirectory);
    $html = "";

    foreach ($files as $file) {
        $filePath = "$photosDirectory/$file";
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ["jpg", "jpeg", "png", "gif"])) {
            $relativeFilePath = str_replace(__DIR__, "", $filePath);

            $filename = htmlspecialchars(pathinfo($file, PATHINFO_FILENAME));
            $sanitizedImage = htmlspecialchars($file);

            $html .= "<div class=\"card\">";
            $html .= "<a href=\"$relativeFilePath\" download>";
            $html .= "<img src=\"$relativeFilePath\" alt=\"$sanitizedImage\" loading=\"lazy\">";
            $html .= "</a>";
            $html .= "<span class=\"card-title\">$filename</span>";
            $html .= "</div>";
        }
    }

    echo $html;
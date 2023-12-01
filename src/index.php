<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" content="width=device-width" name="viewport">
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href)
    </script>
    <style>
        html {
            -webkit-text-size-adjust: 100%;
        }

        html.dark {
            color-scheme: dark;
        }

        body {
            margin: 1.4rem;
        }

        audio,
        video,
        img {
            width: 19.2rem;
            display: block;
            border: 3px dotted #ccc;
            margin: 1rem auto 1rem 0rem;
        }

        p {
            margin: 5px 0;
        }

        a:link {
            text-decoration: none;
        }

        .break-all {
            word-break: break-all;
        }

        .whitespace-pre {
            white-space: pre;
        }

        body {
            color: black;
            background-color: white;
        }

        .folders:link,
        .folders:visited {
            color: blue;
        }

        .files:link,
        .files:visited {
            color: orangered;
        }

        .dark body {
            color: white;
            background-color: #1f1f1f;
        }

        .dark .folders:link,
        .dark .folders:visited {
            color: #23bcf1;
        }


        .dark .files:link,
        .dark .files:visited {
            color: #f6bb25;
        }

        form {
            row-gap: 8px;
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
    </style>
</head>

<body>
    <?php

    error_reporting(0);

    $exts = [
        "video" => [".mp4", ".mkv", ".mov", ".webm"],
        "audio" => [".mp3", ".m4a", ".wav", ".ogg", ".aac"],
        "image" => [".jpg", ".jpeg", ".png", ".webp", ".gif", ".svg"],
    ];

    function format_size($size)
    {
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) return "N/A";
        else return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i];
    }

    $homeDirectory = ".";
    $homeDirectoryLength = strlen($homeDirectory);
    $currentDir = $homeDirectory;

    if ($path = $_REQUEST["path"]) {
        if (substr($path, -1) === "/") $path = substr($path, 0, -1);

        $currentDir = $homeDirectory . $path;
        $showDir = $path;
    }

    $files = [];
    $folders = [];
    $media = $_GET["media"];
    $dark = $_GET["dark"];

    if ($media !== "1") $media = "0";
    if ($dark !== "0") $dark = "1";

    if ($dark !== "0")
        echo "<script>document.documentElement.classList.add('dark')</script>";

    if (!($openedDir = opendir($currentDir)))
        die("<b class'break-all'>Directory does not exist.<b>");

    if ($_FILES) {

        for ($count = 0; $count < count($_FILES["files"]["name"]); $count++) {

            $fileName = $_FILES["files"]["name"][$count];
            $tmpFileName = $_FILES["files"]["tmp_name"][$count];

            if (!$tmpFileName) break;

            echo "<p>Saved $fileName to $showDir</p>";
            move_uploaded_file($tmpFileName, "$currentDir/$fileName");
        }
    }

    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='file' name='files[]' multiple>";
    echo "<input type='submit' value='Upload'>";
    echo "</form>";
    echo "<b class='break-all'>Index of $showDir/ </b>";

    if ($currentDir !== $homeDirectory) {

        $lastSlashIndex = strrpos($showDir, "/");
        $parentDir = substr($showDir, 0, $lastSlashIndex);

        echo "<p>⤶ <a class='folders break-all' href='?dark=$dark&media=$media&path=$parentDir/'>Parent Directory</a></p>";
    }

    while (($item = readdir($openedDir)) !== false) {

        if ($item == "." or $item == "..") continue;

        $path = "$currentDir/$item";

        if (is_dir($path)) {
            $link = "<a class='folders break-all' href='?dark=$dark&media=$media&path=$showDir/$item/'>$item</a>";

            array_push($folders, $link);
        } else {

            if ($media == "1") {

                $ext = strtolower(strrchr($item, "."));

                if (in_array($ext, $exts["image"])) $view = "<img src='$path'>";
                else if (in_array($ext, $exts["audio"])) $view = "<audio controls><source src='$path'></audio>";
                else if (in_array($ext, $exts["video"])) $view = "<video controls><source src='$path'></video>";
            }

            $link = "<a class='files break-all' href='$path'>$item</a>";
            $size = "<span class='whitespace-pre'> ~ " . format_size(filesize($path)) . "</span>";
            array_push($files, $link . $size . $view);

            unset($view);
        }
    }

    natcasesort($folders);
    foreach ($folders as $folder) echo "<p>• $folder</p>";

    natcasesort($files);
    foreach ($files as $file) echo "<p>• $file</p>";

    ?>
</body>

</html>
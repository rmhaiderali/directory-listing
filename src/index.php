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

        video:fullscreen {
            border: none;
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

    // https://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
    function join_paths()
    {
        $paths = array();
        foreach (func_get_args() as $arg) {
            if ($arg !== "") $paths[] = $arg;
        }
        return preg_replace("#/+#", "/", join("/", $paths));
    }

    function nth_last_index_of($haystack, $needle, $nth)
    {
        $len = strlen($haystack);
        $pos = $len;
        for ($i = 0; $i < $nth; $i++) {
            $pos = strrpos($haystack, $needle, $pos - $len - 1);
            if ($pos === false) return false;
        }
        return $pos;
    }

    $homeDirectory = "./";

    $files = [];
    $folders = [];

    $media = isset($_GET["media"]) ? $_GET["media"] : "0";
    $dark = isset($_GET["dark"]) ? $_GET["dark"] : "1";
    $path = isset($_REQUEST["path"]) ? $_REQUEST["path"] : "/";

    $externalDir = join_paths("/", $path, "/");
    $internalDir = join_paths($homeDirectory, $externalDir);

    if ($path !== $externalDir)
        header("Location: ?dark=$dark&media=$media&path=$externalDir", true, 301);

    if ($dark !== "0")
        echo "<script>document.documentElement.classList.add('dark')</script>";

    if (!is_dir($internalDir))
        die("<b class'break-all'>Directory does not exist.<b>");

    if ($_FILES) {

        for ($count = 0; $count < count($_FILES["files"]["name"]); $count++) {

            $fileName = $_FILES["files"]["name"][$count];
            $tmpFileName = $_FILES["files"]["tmp_name"][$count];

            if (!$tmpFileName) break;

            echo "<p>Saved $fileName to $externalDir</p>";
            move_uploaded_file($tmpFileName, "$internalDir/$fileName");
        }
    }

    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='file' name='files[]' multiple>";
    echo "<input type='submit' value='Upload'>";
    echo "</form>";
    echo "<b class='break-all'>Index of $externalDir</b>";

    if ($internalDir !== join_paths($homeDirectory, "/")) {

        $secondLastSlashIndex = nth_last_index_of($externalDir, "/", 2);
        $parentDir = substr($externalDir, 0, $secondLastSlashIndex);

        echo "<p>⤶ <a class='folders break-all' href='?dark=$dark&media=$media&path=$parentDir/'>Parent Directory</a></p>";
    }

    $openedDir = opendir($internalDir);

    while (($item = readdir($openedDir)) !== false) {

        if ($item == "." or $item == "..") continue;

        $itemPath = "$internalDir$item";

        if (is_dir($itemPath)) {
            $link = "<a class='folders break-all' href='?dark=$dark&media=$media&path=$externalDir$item/'>$item</a>";

            array_push($folders, $link);
        } else {

            if ($media == "1") {

                $ext = strtolower(strrchr($item, "."));

                if (in_array($ext, $exts["image"])) $view = "<img src='$itemPath'>";
                else if (in_array($ext, $exts["audio"])) $view = "<audio controls><source src='$itemPath'></audio>";
                else if (in_array($ext, $exts["video"])) $view = "<video controls><source src='$itemPath'></video>";
            }

            $link = "<a class='files break-all' href='$itemPath'>$item</a>";
            $size = "<span class='whitespace-pre'> ~ " . format_size(filesize($itemPath)) . "</span>";
            array_push($files, $link . $size . (isset($view) ? $view : ""));

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
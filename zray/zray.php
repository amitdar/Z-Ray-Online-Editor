<?php

namespace OnlineEditor;

$zre = new \ZRayExtension('Online Editor', true);
$zre->attachAction('showCode', 'OnlineEditor\shutdown', function() {
    if (isset($_POST['filepath'])) {
        if (file_exists($_POST['filepath'])) {
            if (filesize($_POST['filepath']) > 1000000) { // 1MB
                echo 'error: File size exceeded 1MB';
            } else {
                echo file_get_contents($_POST['filepath']);
            }
        } else {
            echo 'error: Cannot access file';
        }
    }
    exit;
});

$zre->attachAction('saveCode', 'OnlineEditor\shutdown', function() {
    if (isset($_POST['filepath']) && isset($_POST['code'])) {
        if (file_exists($_POST['filepath'])) {
            if (is_writable($_POST['filepath'])) {
                file_put_contents($_POST['filepath'], $_POST['code']);
                echo 'saved';
            } else {
                echo 'error: The file is not writable';
            }
        } else {
            echo 'error: Cannot access file';
        }
    }
    exit;
});

$zre->attachAction('list', 'OnlineEditor\shutdown', function() {
    $_POST['path'] = rtrim($_POST['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (isset($_POST['path'])) {
        if (file_exists($_POST['path'])) {
            if (is_dir($_POST['path'])) {
                $files = scandir($_POST['path']);
                $result = array();
                $parentDir = dirname($_POST['path']);
                if ($_POST['path'] != $parentDir && is_dir($parentDir)) {
                    $result[] = array('name' => '..', 'dir' => true, 'path' => realpath(dirname($_POST['path'])), 'size' => 0, 'writable' => false);
                }
                foreach ($files as $file) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $filepath = realpath($_POST['path'] . $file);
                    $isDir = is_dir($filepath);
                    $size = 0;
                    if (! $isDir) {
                        if (file_exists ($filepath)) {
                            $size = filesize($filepath);
                        }
                    }
                    $isWritable = is_writable($filepath);
                    $result[] = array('name' => $file, 'dir' => $isDir, 'path' => $filepath, 'size' => $size, 'writable' => $isWritable);
                }
                echo json_encode($result);
                exit;
            } else {
                echo 'error: Cannot access path';
                exit;
            }
        } else {
            echo 'error: Cannot access path';
            exit;
        }
    }

    echo 'error: Cannot access path';
    exit;
});

$zre->setMetadata(array(
	'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'logo.png',
	'actionsBaseUrl' => $_SERVER["REQUEST_URI"],
    'initDir' => getcwd(),
));

function shutdown() {}
register_shutdown_function('OnlineEditor\shutdown');
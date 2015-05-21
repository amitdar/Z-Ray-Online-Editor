<?php

namespace OnlineEditor;

$zre = new \ZRayExtension('Online Editor', true);
$zre->attachAction('showCode', 'OnlineEditor\shutdown', function() {
    if (isset($_POST['filepath'])) {
        if (file_exists($_POST['filepath'])) {
            echo file_get_contents($_POST['filepath']);
        } else {
            echo 'error: Cannot access file';
        }
    }
    exit;
});

$zre->attachAction('saveCode', 'OnlineIDE\shutdown', function() {
    if (isset($_POST['filepath']) && isset($_POST['code'])) {
        if (file_exists($_POST['filepath'])) {
            if (is_writable($_POST['filepath'])) {
                file_put_contents($_POST['filepath'], $_POST['code']);
            } else {
                echo 'error: The file is not writable';
            }
        } else {
            echo 'error: Cannot access file';
        }
    }
    
    exit;
});

$zre->setMetadata(array(
	'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'logo.png',
	'actionsBaseUrl' => $_SERVER["REQUEST_URI"],
));

function shutdown() {}
register_shutdown_function('OnlineEditor\shutdown');
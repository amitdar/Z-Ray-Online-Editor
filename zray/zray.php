<?php

namespace OnlineEditor;

class Editor {
    private $postParams;
    
    public function __construct() {
        $this->postParams = $_POST;
    }
    
    public function showCode() {
        if (isset($this->postParams['filepath'])) {
            if (file_exists($this->postParams['filepath'])) {
                if (filesize($this->postParams['filepath']) > 1000000) { // 1MB
                    echo 'error: File size exceeded 1MB';
                } else {
                    echo file_get_contents($this->postParams['filepath']);
                }
            } else {
                echo 'error: Cannot access file';
            }
        }
        exit;
    }

    public function saveCode() {
        if (isset($this->postParams['filepath']) && isset($this->postParams['code'])) {
            if (file_exists($this->postParams['filepath'])) {
                if (is_writable($this->postParams['filepath'])) {
                    file_put_contents($this->postParams['filepath'], $this->postParams['code']);
                    echo 'saved';
                } else {
                    echo 'error: The file is not writable';
                }
            } else {
                echo 'error: Cannot access file';
            }
        }
        exit;
    }
    
    public function listFiles() {
        $this->postParams['path'] = rtrim($this->postParams['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (isset($this->postParams['path'])) {
            if (file_exists($this->postParams['path'])) {
                if (is_dir($this->postParams['path'])) {
                    $files = scandir($this->postParams['path']);
                    $result = array();
                    $parentDir = dirname($this->postParams['path']);
                    if ($this->postParams['path'] != $parentDir && is_dir($parentDir)) {
                        $result[] = array('name' => '..', 'dir' => true, 'path' => realpath(dirname($this->postParams['path'])), 'size' => 0, 'writable' => false);
                    }
                    foreach ($files as $file) {
                        if ($file == '.' || $file == '..') {
                            continue;
                        }
                        $filepath = realpath($this->postParams['path'] . $file);
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
    }
}

$zre = new \ZRayExtension('Online Editor', true);

$editor = new Editor();
$zre->attachAction('showCode', 'OnlineEditor\shutdown', array($editor, 'showCode'));
$zre->attachAction('saveCode', 'OnlineEditor\shutdown', array($editor, 'saveCode'));
$zre->attachAction('list', 'OnlineEditor\shutdown', array($editor, 'listFiles'));

$zre->setMetadata(array(
	'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'logo.png',
	'actionsBaseUrl' => $_SERVER["REQUEST_URI"],
    'initDir' => getcwd(),
));

function shutdown() {}
if (isset($_POST['ZRayAction'])) {
    register_shutdown_function('OnlineEditor\shutdown');
}
<?php
define('PUBLISH_INPUT_ROOT', '/home/ftpuser/path/to/releases/Application\ Files');
define('PUBLISH_OUTPUT_ROOT', '/var/www/pub.yoursite.com/releases');
define('PUBLISH_XML_FEED', '/var/www/pub.yoursite.com/release.xml');

define('WINFORM_PREFIX', 'YourProject_');
define('BASE_URL', 'http://pub.yoursite.com');

/******* helper functions *******/
// copy recursive
function copy_recursive($source, $dest){
    if(is_dir($source)) {
        $dir_handle=opendir($source);
        while($file=readdir($dir_handle)){
            if($file!="." && $file!=".."){
                if(is_dir($source."/".$file)){
                    if(!is_dir($dest."/".$file)){
                        mkdir($dest."/".$file);
                    }
                    copy_recursive($source."/".$file, $dest."/".$file);
                } else {
                    copy($source."/".$file, $dest."/".$file);
                }
            }
        }
        closedir($dir_handle);
    } else {
        copy($source, $dest);
    }
}

function add_file_update_task(&$tasks, $relative_path = '', $abs_file) {
    $file = basename($abs_file);
    if ($relative_path) $file = rtrim($relative_path, '/') . '/' . $file;

    $file_update = $tasks->addChild('FileUpdateTask');
    $file_update->addAttribute('localPath', $file);
    $file_update->addAttribute('lastModified', filemtime($abs_file));
    $file_update->addAttribute('fileSize', filesize($abs_file));

    $conditions = $file_update->addChild('Conditions');

    $file_exists_cond = $conditions->addChild('FileExistsCondition');
    $file_exists_cond->addAttribute('type', 'or-not');

    $file_checksum_cond = $conditions->addChild('FileChecksumCondition');
    $file_checksum_cond->addAttribute('type', 'or-not');
    $file_checksum_cond->addAttribute('checksumType', 'sha256');
    $file_checksum_cond->addAttribute('checksum', hash_file('sha256', $abs_file));
}

/******* script start here *******/

$input_folders = glob(PUBLISH_INPUT_ROOT . DIRECTORY_SEPARATOR . WINFORM_PREFIX . '*');
$input_folders = array_combine($input_folders, array_map('filemtime', $input_folders));
arsort($input_folders);

// latest folder from the published dir
$latest_folder_abs_path = key($input_folders);
$latest_folder = basename($latest_folder_abs_path);
$chunks = explode('_', str_replace(WINFORM_PREFIX, '', $latest_folder));
$latest_version = implode('.', $chunks);

$latest_output_folder = 'v' . $latest_version;
$latest_output_folder_abs_path = PUBLISH_OUTPUT_ROOT . DIRECTORY_SEPARATOR . $latest_output_folder;

// if the output folder is the latest version, end of story
if (is_dir($latest_output_folder_abs_path)) return;

mkdir($latest_output_folder_abs_path);
copy_recursive($latest_folder_abs_path, $latest_output_folder_abs_path);

$base_url = BASE_URL . '/releases/' . $latest_output_folder;
$files = glob($latest_output_folder_abs_path . DIRECTORY_SEPARATOR . '*');

$xml = new SimpleXMLElement('<Feed/>');
$xml->addAttribute('BaseUrl', $base_url);
$tasks = $xml->addChild('Tasks');

foreach ($files as $abs_file) {
    if (is_dir($abs_file)) {
        $directory = rtrim($abs_file, '/');
        foreach (glob($directory . '/*') as $sub_file) {
            add_file_update_task($tasks, basename($directory), $sub_file);
        }
        continue;
    }
    add_file_update_task($tasks, '', $abs_file);
}

$dom = dom_import_simplexml($xml);
$dom->xmlEndoding = 'UTF-8';
$dom = $dom->ownerDocument;
$dom->formatOutput = true;
file_put_contents(PUBLISH_XML_FEED, $dom->saveXml());

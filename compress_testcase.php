<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db_password_path='/etc/mysql/mysql.conf.d/.sqlpwd';
$db_name = 'db_ART';
$db_table = 'tbl_testCase';
$dir = __DIR__;
$testcase_dir = $dir . '/ART/storage/testcases';
$callback_dir = $dir . '/ART/storage/callbacks';
$new_path = $dir . '/ART/vendor/bit-control/artsys/src/testcases';

if (is_dir($testcase_dir) && is_dir($callback_dir)) {
    $fh = opendir($testcase_dir);
    while (FALSE !== ($file = readdir($fh))) {
        if (!in_array($file, ['.', '..'])) { 
            if (preg_match("/skeleton/", $file, $matches)) {
                $skeleton = $file;
                copy($testcase_dir . "/{$skeleton}", $new_path . "/$skeleton");
                $skeleton = str_replace("testcase", "callback", $file);
                copy($callback_dir . "/{$skeleton}", $new_path . "/$skeleton");
            } else {
                $output = null;
                $testCase_ID = str_replace('.php', '', $file);
                $folder = $new_path . "/{$testCase_ID}";
                if (!file_exists($folder)) {
                    mkdir($folder);
                }
                @copy($testcase_dir . "/{$file}", $folder . "/tc-{$testCase_ID}.php");
                @copy($callback_dir . "/{$file}", $folder . "/cb-{$testCase_ID}.php");
                if ($testCase_ID) {
                    $command = "mysqldump --defaults-extra-file={$db_password_path} -nt --skip-opt {$db_name} {$db_table} --where=testCase_ID='{$testCase_ID}' > {$folder}/{$testCase_ID}.sql";
                    exec($command);
                }
            }
        }
    }
    //testcase category
    $db_table = 'tbl_testCaseCategory';
    $command = "mysqldump --defaults-extra-file={$db_password_path} -nt --skip-opt {$db_name} {$db_table} > {$new_path}/testcase_category.sql";
    exec($command);
    exit;
}
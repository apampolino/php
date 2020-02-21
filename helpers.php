<?php

const SECONDS_PER_YEAR = 31536000;
const SECONDS_PER_MONTH = 2592000;
const SECONDS_PER_WEEK = 604800;
const SECONDS_PER_DAY = 86400;
const SECONDS_PER_HOUR = 3600;
const SECONDS_PER_MINUTE = 60;

if (!function_exists('secondsToDay')) {
    
    function secondsToDay($seconds) {

        $year = floor($seconds / SECONDS_PER_YEAR);

        $excess = ($seconds % SECONDS_PER_YEAR);

        $month = floor($excess / SECONDS_PER_MONTH);

        $excess = ($excess % SECONDS_PER_MONTH);

        $week = floor($excess / SECONDS_PER_WEEK);

        $excess = ($excess % SECONDS_PER_WEEK);

        $day = floor($excess / SECONDS_PER_DAY);

        $excess = ($excess % SECONDS_PER_DAY);

        $hour = floor($excess / SECONDS_PER_HOUR);

        $excess = ($excess % SECONDS_PER_HOUR);

        $minute = floor($excess / SECONDS_PER_MINUTE);

        $seconds = $excess % SECONDS_PER_MINUTE;

        return "{$year} y {$month} m {$week} w {$day} d {$hour} h {$minute} m {$seconds} s";
    }
}

if (!function_exists('dump')) {

    function dump(...$vars) {

        echo "<pre>";
        var_dump($vars);
        echo "</pre>";
    }
}

if (!function_exists('dd')) {

    function dd(...$vars) {

        call_user_func_array('dump', $vars);
        die();
    }
}

if (!function_exists('check_separator')) {

    function check_separator($string) {

        $separator = substr($string, strlen($string) - 1, 1);

        if ($separator != '/') {

            $string .= '/';
        }

        return $string;
    }
}

if (!function_exists('zip')) {

    function zip($zip_filename = '', $settings = []) {

        if ($zip_filename && $settings) {

            $errors = [];

            $save_path = (!empty($settings['save_path'])) ? check_separator($settings['save_path']) : __DIR__;

            $zip_filename = $save_path . $zip_filename;

            $zip = new ZipArchive;

            if ($zip->open($zip_filename, ZipArchive::CREATE|ZipArchive::OVERWRITE)) {

                foreach ($settings['path'] as $p) {

                    $include_path = check_separator($p);

                    foreach ($settings['files'] as $f) {

                        $file = $include_path . $f;

                        if (file_exists($file)) {

                            $zip->addFile($file, $f);

                        } else {

                            $errors[] = "File not found on {$include_path}{$f}";
                        }
                    }
                }

                $zip->close();
            }

            unset($zip);

            return $errors;
        }
    }
}

if (!function_exists('file_change_extension')) 
{
    function file_change_extension($filename = '', $validate_extension = '', $new_extension = '')
    {   
        preg_match("/\.(.*)/", $filename, $matches);

        if ($matches[0] === $validate_extension) {

            $tmp = preg_replace("/\.(.*)/", $new_extension, $filename);
        }

        return $tmp;
    }
}

if (!function_exists('truncateFile')) {

    function truncateFile($filename, $path) {

        $root_dir = check_separator(realpath(__DIR__  . "/../{$path}"));

        $file = $root_dir . $filename;

        $fh = fopen($file, 'w');

        if ($fh) {

            fclose($fh);
        }

        unset($fh);
    }
}

if (!function_exists('writeToFile')) {
    
    function writeToFile($filename, $path, $data = '', $mode = 'a+') {

        $root_dir = check_separator(realpath(__DIR__  . "/../$path"));

        $file = $root_dir . $filename;

        $fh = fopen($file, $mode);

        if ($fh) {

            fwrite($fh, $data);

            fclose($fh);
        }

        unset($fh);
    }
}

if (!function_exists('safeSerialization')) {

    function safeSerialization($item = null) {

        if ($item) {
            
            return base64_encode(serialize($item));
        }
    }
}

if (!function_exists('safeUnserialization')) {
    
    function safeUnserialization($item = null) {

        if ($item) {

            return unserialize(base64_decode($item));
        }
    }
}

if (! function_exists('checkFile')) {

    function checkFile($filename = '', $path = '') {

        $file = null;

        $root_dir = __DIR__ . "/../" . $path;

        if (substr($root_dir, 0, (strlen($root_dir) - 1)) !== "/") {

            $root_dir .= "/";
        }

        $file = $root_dir . $filename;

        if (file_exists($file)) {

            return true;
        }

        return false;
    }
}

if (! function_exists('copyFile')) {

    function copyFile($parent = '', $filename = '', $path = '') {

        $file = null;

        $path = (substr($path, 0, 1) == "/") ? substr($path, 1, strlen($path)) : $path;

        $root_dir = __DIR__ . "/../" . $path;

        if (substr($root_dir, 0, (strlen($root_dir) - 1)) !== "/") {

            $root_dir .= "/";
        }

        if ($parent) {

            $source = $root_dir . $parent;

            $dest =  $root_dir . $filename;

            if (file_exists($source)) {
                
                return copy($source, $dest);
            }
        }

        return false;
    }
}

if (!function_exists('getRootEnv')) {
    
    function getRootEnv($key = '', $default = '') {

        $env = file_get_contents(__DIR__ . '/../.env');

        $windows_line_ending = preg_match_all("/\x0D\x0A/", $env, $matches);
        
        $env = ($windows_line_ending) ? array_filter(explode("\r\n", $env)) : array_filter(explode("\n", $env));
        
        $vars = [];

        foreach($env as $v) {

            $row = explode("=", $v);

            $vars[$row[0]] = $row[1];
        }

        if (in_array($key, array_keys($vars))) {

            return (!empty($vars[$key])) ? $vars[$key] : $default;
        }

        return $default;
    }
}

if (!function_exists('addZero')) {

    function addZero($string) {

        $var = strlen($string) > 2 ? $string : str_pad($string, 2, "0", STR_PAD_LEFT);

        return $var;
    }
}

if (!function_exists('random_date')) {

    function random_date($start = '', $end = '') {

        $r_start = strtotime($start);
        
        $r_end = strtotime($end);

        $r_date = rand($r_start, $r_end);

        return Carbon::createFromTimestamp($r_date);
    }
}

if (!function_exists('execute_script')) {

    function execute_script($file, $pre = '', $string_options = '')
    {
        if (file_exists($file)) {

            unset($output);

            exec("{$pre} {$file} {$string_options}", $output, $status);

            return $output;
        
        } else {

            return false;
        }
    }
}

if (!function_exists('clean_xml_attributes') {

    function clean_xml_attributes($xml_string = '', $format = TRUE)
    {
        $file = realpath(getcwd() . '/../config/xml.php');

        if (file_exists($file)) {

            $xml_list = require $file;

            foreach ($xml_list as $xs) {

                $xml_string = str_replace($xs['regex'], $xs['replacement'], $xml_string);
            }
        }

        if ($format) {

            $xml_string = preg_replace("/(><)/", ">\n<", $xml_string);
        }

        return $xml_string;
    }
}

if (!function_exists('readXML')) {

    function readXML($path = '', $object = FALSE) {

        try {

            $xml = simplexml_load_file($path);

            if ($object) {

                return $xml;
            }

            return json_decode(json_encode($xml), true);

        } catch (Exception $e) {

            return $e;
        }
    }
}

if (!function_exists('generate_key')) {

    function generate_key($key = '', $raw = FALSE) {

        return md5($key . strtotime('now'), $raw);
    }
}

if (!function_exists('array_recursive')) {

    function array_recursive($key = '', Array $val, Closure $callback) {

        if (is_array($val)) {
            
            foreach ($val as $k => $v) {

                array_recursive($k, $v, $callback);
            }

        } else {

            if ($callback && is_callable($callback)) {

                return $callback($key, $val);
            }

            return $key;
        }
    }
}

if (!function_exists('alias_config'))
{
    function alias_config()
    {
        $file = realpath(getcwd() . '/../config/alias.php');

        if (file_exists($file)) {

            return require $file;
        }
    }
}

if (!function_exists('getAliasKey'))
{
    function getAliasKey($list = [], $key = '', $default = '') 
    {
        $alias = alias_config();

        $list_path = $alias;

        foreach ($list as $k => $v) {

            $list_path = $list_path[$v];
        }

        if (array_key_exists($key, $list_path)) {

            return $list_path[$key];
        }

        return $default;
    }
}

if (!function_exists('xml_structure'))
{
    function xml_structure($alias_pathlist = [], $attributes = [], $inline = [], $multi_elems = []) {

        $xml_structure = ['inline' => [
                    'xmlns' => 'http://dummydomain.telekom.de/RDQ/LineProvisioning_1', 
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'], 
                'attributes' => []
            ];

        if ($inline) {

            foreach ($inline as $key => $val) {
                
                if (array_key_exists($key, $xml_structure['inline'])) {

                    $xml_structure['inline'][$key] = $val;

                } else {

                    $xml_structure['inline'][$key] = $val;
                }
            }
        }

        if ($attributes) {

            foreach ($attributes as $key => $val) {

                if (in_array($key, $multi_elems)) {

                    foreach ($val as $ckey => $cval) {
                        
                        $xml_structure['attributes'][getAliasKey($alias_pathlist, $key, $key)][] = $cval;
                    }

                } else {

                    $xml_structure['attributes'][getAliasKey($alias_pathlist, $key, $key)] = $val;
                }
            }
        }

        return $xml_structure;
    }
}

if (!function_exists('array_to_xml')) {

    function array_to_xml($path = __DIR__, $xml_string = '<sample></sample>', $data = array(), $multi_elems = [], $override_name = '')
    {
        if (!empty($data)) {

            $xml = new SimpleXMLElement($xml_string);

            foreach ($data as $key => $value) {

                if ($key == 'inline') {

                    foreach ($value as $key_attr => $val_attr) {

                        $xml->addAttribute($key_attr, $val_attr);
                    }

                } else {

                    foreach ($value as $key_elem => $val_elem) {

                        if (in_array($key_elem, $multi_elems)) {

                            foreach ($val_elem as $ngKey => $ngVal) {
                                
                                recurseXMLChildren($key_elem, $ngVal, $xml);
                            }

                        } else {

                            recurseXMLChildren($key_elem, $val_elem, $xml);
                        }
                    }
                }
            }

            $filename = (!empty($override_name)) ? $override_name : $xml->getName() . '.xml';

            $dom = new DomDocument('1.0', 'iso-8859-1');

            $dom->preserveWhiteSpace = TRUE;

            $dom->formatOutput = TRUE;

            $dom->loadXML($xml->asXML());

            return ($dom->save($path . '/' . $filename)) ? TRUE : FALSE;
        }
    }
}

if (!function_exists('recurseXMLChildren'))
{
    function recurseXMLChildren($key = '', $val = '', SimpleXMLElement $parent) 
    {
        if (is_array($val)) {

            $key_parent = $parent->addChild($key);

            foreach ($val as $key_item => $value_item) {
                
                recurseXMLChildren($key_item, $value_item, $key_parent);
            }

        } else {

            $parent->addChild($key, $val);
        }
    }
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_random_str($length = 8, $num = false, $lower = false, $upper = false){
    $num_chars = "0123456789";
    $lower_chars = "abcdefghijklmnopqrstuvwxyz";
    $upper_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    $str = "";
    $result_str =  $lower_chars[ mt_rand(0, strlen($lower_chars) - 1) ];
    if ($num) {
        $str .= $num_chars;
    }
    if ($lower) {
        $str .= $lower_chars;
    }
    if ($upper) {
        $str .= $upper_chars;
    }
    for ($index=0; $index < $length - 1; $index++) {
            $result_str .= $str[ mt_rand(0, strlen($str) - 1) ];
    }
    return $result_str;
}

/**
 * 内容清洗
 * @param  string $content 传入的字符串内容
 * @return string          清洗后的字符串
 */
function clean_content($content){
    $content = addslashes($content);
    $filter_char = array('&', '>', '<', '\\', '/', '|', '^', '`', '"', "'");
    foreach ($filter_char as $key => $value) {
        $content = str_replace($value, "", $content);
    }
    return $content;
}

/* End of file Tools.php */
/* Location: ./application/helpers/Tools.php */ ?>
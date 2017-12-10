<?php

function NETTOOLS_formatResult($result) {
    $result = str_replace(array("\r\n", "\n", "\r"), '<br' . XHTML . '>', $result);
    
    if (is_callable('mb_detect_encoding') && is_callable('mb_convert_encoding')) {
        $encoding = mb_detect_encoding($result);
        $result = mb_convert_encoding($result, COM_getEncodingt(), $encoding);
    }

    return $result;
}

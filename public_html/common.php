<?php

function NETTOOLS_formatResult($result) {
    $result = str_replace(array("\r\n", "\n", "\r"), '<br' . XHTML . '>', $result);
    
    if (is_callable('mb_detect_encoding') && is_callable('mb_convert_encoding')) {
        $encoding = mb_detect_encoding(
            $result, array(
                'UTF-8', 'UTF-16',
                'SJIS', 'EUC-JP',
                'EUC-KR',
                'KOI8-R', 'KOI8-U',
                'BIG-5', 'GB18030', 'HZ', 'EUC-TW',
                'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-5', 'ISO-8859-9', 'ISO-8859-15',
            )
        );

        if ($encoding !== false) {
            $result = mb_convert_encoding($result, COM_getEncodingt(), $encoding);
        }
    }

    return $result;
}

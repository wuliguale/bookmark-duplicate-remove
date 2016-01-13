<?php

//src bookmark file
$file = 'bookmarks.html';
//destination bookmark file
$file2 = str_replace('.html', '-' . date('YmdHis') . '.html', $file);

//replace or not when url duplicate
$replace = true;

$data = file_get_contents($file);

$handle = fopen($file, 'rb');

while(!feof($handle)) {
    $str = fgets($handle);

    if(strpos($str, '<DT><A HREF=') !== false) {
        if(preg_match('/\bHREF="[^<].*?"/', $str, $url)) {
            $url = reset($url);

            $patten = str_replace(
                array(
                    '/',
                    '.',
                    '-',
                    '?',
                    '#',
                    '|',
                ),
                array(
                    '\/',
                    '\.',
                    '\-',
                    '\?',
                    '\#',
                    '\|',
                ),
                $url
            );

            $patten = '/ {1,}<DT><A ' . $patten . '.*?<\/A>\r\n/';
            $matchCount = preg_match_all($patten, $data);

            //duplicate
            if($matchCount > 1) {
                $index = 0;

                $data = preg_replace_callback($patten,
                    function ($matches) use ($matchCount, &$index, $replace){
                        $index++;

                        if(($index == 1 && !$replace) || ($index == $matchCount && $replace)) {
                            return $matches[0];
                        }
                        else {
                            return '';
                        }
                    },
                    $data
                );
            }
        }
    }
}
fclose($handle);

file_put_contents($file2, $data);
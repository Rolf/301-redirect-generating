<form method="post">
    <input name="old" placeholder="Ссылка на раздел старого сайта">
    <input name="new" placeholder="Ссылка на раздел нового сайта">
    <button>Импорт</button>
</form>

<?php

if(!empty($_POST)) {
    $result = [];

    // паттерны
    $patternOld = '/<a class="title" href="(.*?)".*?>.*?><br\/>(.*?)<\/a>/msi';
    $patternNew = '/<a class="name-item link-dark" href="(.*?)">(.*?)<\/a>/msi';

    // урлы, пришедшие из реквеста
    $urlOld = $_POST['old'];
    $urlNew = $_POST['new'];

    // получаем контент
    $old = parse($patternOld, $urlOld, true);
    $new = parse($patternNew, $urlNew);

    // выводим сгенерированные правила. совпадения по имени.
    foreach ($old as $oldArray) {
        foreach ($new as $newArray) {
            if ($oldArray['name'] == $newArray['name']) {
                echo 'Redirect 301 ' . $oldArray['url'] . ' ' . $newArray['url'] . '' . "<br>";
            }
        }
    }
}

/**
 * Разбор и возвращение контента
 *
 * @param $pattern
 * @param $url
 * @param bool $encode - перевести в кодировку utf8
 * @return array
 */
function parse($pattern, $url, $encode = false)
{
    $result = [];

    preg_match_all($pattern, file_get_contents($url), $matches);

    foreach ($matches[2] as $key => $text) {
        if ($encode) {
            $coding = mb_detect_encoding($text);
            $text = mb_convert_encoding($text, 'utf-8', 'cp1251');
            $text = iconv($coding, 'UTF-8', $text);
        }

        $result[$key] = [
            'name' => mb_strtolower(trim($text)),
            'url' => urldecode($matches[1][$key]),
        ];
    }

    return $result;
}

/**
 * Для отладки
 *
 * @param $array
 */
function dd($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
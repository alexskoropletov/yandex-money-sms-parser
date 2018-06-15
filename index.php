<?php
/**
 * 2018-06-15
 */
echo "[<]" . PHP_EOL . PHP_EOL;
if (isset($argv[1])) {
    $patterns = [
        'code'   => [
            'regex'  => '/([0-9]{4}(?![0-9]))/',
            'after'  => function($message) {
                // убираем пробелы и переносы строки в сумме платежа
                return str_replace([" ", "\n", "\t", "\r"], '', $message);
            },
            'name'   => 'Код подтверждения',
            'result' => false,
        ],
        'amount' => [
            'regex'  => '/([0-9]{1,6}\,[0-9]{2}р)/',
            'name'   => 'Сумма к оплате',
            'result' => false,
        ],
        'wallet' => [
            'regex'  => '/([0-9]{14,15})/',
            'name'   => 'ID кошелька',
            'result' => false,
        ],
    ];
    if ($message = file_get_contents($argv[1])) {
        echo sprintf("SMS сообщение: %s'%s'", PHP_EOL, $message) . PHP_EOL . PHP_EOL;
        findUsefulStuffInMessage($patterns, $message);
    } else {
        echo "[!] Ошибка: не удалось прочитать файл с сообщением" . PHP_EOL;
    }
} else {
    echo "[!] Ошибка: не задан путь к файлу с сообщением" . PHP_EOL;
}
echo PHP_EOL . "[>]" . PHP_EOL;

/**
 * @param $patterns array
 * @param $message string
 */
function findUsefulStuffInMessage($patterns, $message) {
    foreach ($patterns as $pattern_key => $pattern) {
        preg_match($pattern['regex'], $message, $matches);
        if (isset($matches[0])) {
            echo sprintf('%s: %s', $pattern['name'], $matches[0]) . PHP_EOL;
        } else {
            echo sprintf("[!] Ошибка: не удалось найти %s", $pattern['name']) . PHP_EOL;
        }
        if (isset($pattern['after'])) {
            $message = $pattern['after']($message);
        }
    }
}

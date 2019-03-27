<?php
sleep(1);
echo json_encode([
    $_GET['q'] => [
        'html' => 'query: <strong>'.strtoupper($_GET['q']).'</strong>',
        'text' => strtoupper($_GET['q'])
    ],
    'foo' => 'FOO',
    'bar' => 'BAR',
    'baz' => 'BAZ'
]);

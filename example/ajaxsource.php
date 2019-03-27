<?php
sleep(1);
echo json_encode([
    'foo' => 'FOO',
    'bar' => 'BAR',
    'baz' => 'BAZ',
    $_GET['q'] => [
        'html' => 'query: <strong>'.strtoupper($_GET['q']).'</strong>',
        'text' => strtoupper($_GET['q'])
    ],
]);

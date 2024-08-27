<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/core')
    ->in(__DIR__ . '/modules')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);

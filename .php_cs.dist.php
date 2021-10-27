<?php

$finder = (new PhpCsFixer\Finder())
    ->files()
    ->in(__DIR__)
    ->exclude(__DIR__.'/vendor')
;

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;

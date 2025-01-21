<?php

$finder = (new PhpCsFixer\Finder())
    // ->in(__DIR__)
    //juste fix two repositories src and tests
    ->in(__DIR__ .'/tests')
    ->in(__DIR__ . '/src')
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;

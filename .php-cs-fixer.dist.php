<?php
$finder = PhpCsFixer\Finder::create()->in(__DIR__.'/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PHP84Migration' => true,
        'yoda_style' => false,
    ])
    ->setFinder($finder);

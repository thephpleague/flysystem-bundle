<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules(array(
        '@Symfony' => true,
        'phpdoc_annotation_without_dot' => false,
    ))
    ->setFinder($finder)
;

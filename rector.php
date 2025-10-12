<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/public',
    ])
    ->withPHPStanConfigs(
        match (true) {
            file_exists(__DIR__ . '/phpstan.dist.neon') => [__DIR__ . '/phpstan.dist.neon'],
            file_exists(__DIR__ . '/phpstan.neon') => [__DIR__ . '/phpstan.neon'],
            default => []
        }
    )
    ->withParallel()
    ->withCache(__DIR__ . '/var/cache/rector')
    ->withImportNames()
    ->withRules([
        PreferPHPUnitThisCallRector::class,
    ])
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true
    )
    ->withComposerBased(symfony: true)
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ]);

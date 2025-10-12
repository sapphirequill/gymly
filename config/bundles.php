<?php

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Baldinof\RoadRunnerBundle\BaldinofRoadRunnerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;
use ApiPlatform\Symfony\Bundle\ApiPlatformBundle;

return [
    FrameworkBundle::class => ['all' => true],
    BaldinofRoadRunnerBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    NelmioCorsBundle::class => ['all' => true],
    ApiPlatformBundle::class => ['all' => true],
];

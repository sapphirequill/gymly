<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\MatchOneOfTheseNames;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\RuleBuilders\Architecture\Architecture;
use Arkitect\Rules\Rule;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__.'/src');

    $architectureRules = Architecture::withComponents()
        ->component('Domain')->definedBy('App\\Domain\\*')
        ->component('Application')->definedBy('App\\Application\\*')
        ->component('Infrastructure')->definedBy('App\\Infrastructure\\*')
        ->component('UserInterface')->definedBy('App\\UserInterface\\*')

        ->where('Domain')->shouldNotDependOnAnyComponent()
        ->where('Application')->mayDependOnComponents('Domain')
        ->where('Infrastructure')->mayDependOnComponents('Domain', 'Application')
        ->where('UserInterface')->mayDependOnComponents('Domain', 'Application')
        ->rules();

    $applicationCommandNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces(
            'App\\Application\\Command'
        ))
        ->should(new MatchOneOfTheseNames(['*Command', '*Handler']))
        ->because('Classes in src/Application/Command must end with Command or Handler');

    $applicationQueryNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces(
            'App\\Application\\Query'
        ))
        ->should(new MatchOneOfTheseNames(['*Query', '*Handler']))
        ->because('Classes in src/Application/Query must end with Query or Handler');

    $uiResourceNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\UserInterface\\Rest\\Api\\Resource'))
        ->should(new HaveNameMatching('*Resource'))
        ->because('Resources must end with Resource');

    $uiProcessorNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\UserInterface\\Rest\\Api\\State\\Processor'))
        ->should(new HaveNameMatching('*Processor'))
        ->because('Processors must end with Processor');

    $uiProviderNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\UserInterface\\Rest\\Api\\State\\Provider'))
        ->should(new HaveNameMatching('*Provider'))
        ->because('Providers must end with Provider');

    $uiRequestNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\UserInterface\\Rest\\Request'))
        ->should(new MatchOneOfTheseNames(['*Request', '*Input']))
        ->because('Requests must end with Request');

    $uiResponseNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\UserInterface\\Rest\\Response'))
        ->should(new HaveNameMatching('*Response'))
        ->because('Responses must end with Response');

    $serializerNormalizerNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\Infrastructure\\Serializer\\Normalizer'))
        ->should(new HaveNameMatching('*Normalizer'))
        ->because('Normalizers must end with Normalizer');

    $projectionProjectorNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\Infrastructure\\Persistence\\Projection\\Projector'))
        ->should(new HaveNameMatching('*Projector'))
        ->because('Projectors must end with Projector');

    $doctrineProjectionRepositoryNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\Infrastructure\\Persistence\\Projection\\Doctrine\\Repository'))
        ->should(new HaveNameMatching('*Repository'))
        ->because('Doctrine Projection repositories must end with Repository');

    $eventStoreDoctrineRepositoryNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\Infrastructure\\Persistence\\EventStore\\Doctrine\\Repository'))
        ->should(new HaveNameMatching('*Repository'))
        ->because('EventStore Doctrine repositories must end with Repository');

    $doctrineFinderNaming = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\\Infrastructure\\Persistence\\Finder\\Doctrine'))
        ->should(new HaveNameMatching('*Finder'))
        ->because('Doctrine finders must end with Finder');

    $allRules = array_merge(
        iterator_to_array($architectureRules),
        [
            $applicationCommandNaming,
            $applicationQueryNaming,
            $uiResourceNaming,
            $uiProcessorNaming,
            $uiProviderNaming,
            $uiRequestNaming,
            $uiResponseNaming,
            $serializerNormalizerNaming,
            $projectionProjectorNaming,
            $doctrineProjectionRepositoryNaming,
            $eventStoreDoctrineRepositoryNaming,
            $doctrineFinderNaming,
        ]
    );

    $config->add($classSet, ...$allRules);
};

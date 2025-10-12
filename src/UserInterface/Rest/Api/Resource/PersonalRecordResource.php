<?php

declare(strict_types=1);

namespace App\UserInterface\Rest\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Application\ReadModel\PersonalRecordReadModel;
use App\UserInterface\Rest\Api\State\Provider\PersonalRecordsProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/personal-records',
            output: PersonalRecordReadModel::class,
            provider: PersonalRecordsProvider::class,
        ),
    ],
    paginationEnabled: false,
)]
final class PersonalRecordResource
{
}

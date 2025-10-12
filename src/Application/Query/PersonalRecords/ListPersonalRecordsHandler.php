<?php

declare(strict_types=1);

namespace App\Application\Query\PersonalRecords;

use App\Application\Port\PersonalRecordFinder;
use App\Application\ReadModel\PersonalRecordReadModel;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListPersonalRecordsHandler
{
    public function __construct(private PersonalRecordFinder $finder)
    {
    }

    /** @return PersonalRecordReadModel[] */
    public function __invoke(ListPersonalRecordsQuery $query): array
    {
        return $this->finder->all();
    }
}

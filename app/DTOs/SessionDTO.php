<?php declare(strict_types=1);

namespace App\DTOs;

final readonly class SessionDTO
{
    /**
     * @param int $userId
     * @param string $sourceType
     * @param string $sourcePath
     * @param int|null $duration
     * @param float|null $confidenceThreshold
     */
    public function __construct(
        public int    $userId,
        public string $sourceType,
        public string $sourcePath,
        public ?int   $duration = null,
        public ?float $confidenceThreshold = 0.5,
    )
    {
    }
}


<?php

namespace App\Http\Traits;

trait HasRepositories
{
    protected array $resolvedRepositories = [];

    public function __get(string $name)
    {
        if (isset($this->resolvedRepositories[$name])) {
            return $this->resolvedRepositories[$name];
        }

        return $this->resolvedRepositories[$name] = match ($name) {
            'videoSessionRepository' => app(\App\Repositories\VideoSessionRepository::class),
            'analysisReportRepository' => app(\App\Repositories\AnalysisReportRepository::class),
            default => throw new \RuntimeException("Unknown repository: {$name}")
        };
    }
}


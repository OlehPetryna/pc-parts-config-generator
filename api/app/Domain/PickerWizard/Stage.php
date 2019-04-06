<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;

use App\Domain\PcParts\PcPart;

class Stage
{
    private $config;
    private $idx;

    public function __construct(int $stage = 0)
    {
        $this->idx = $stage;
        $this->readConfig();
    }

    public function buildDummyPart(): PcPart
    {
        $stageClassName = $this->config[$this->idx];

        $class = PcPart::ENTITIES_NAMESPACE . $stageClassName;
        return new $class();
    }

    public function getName(): string
    {
        return $this->config[$this->idx];
    }

    public function getStagesCount(): int
    {
        return count($this->getAllStages());
    }

    public function getAllStages(): array
    {
        return $this->config;
    }

    public function getIdx(): int
    {
        return $this->idx;
    }

    private function readConfig(): void
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    }
}
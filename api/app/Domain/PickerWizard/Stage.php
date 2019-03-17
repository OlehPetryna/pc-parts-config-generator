<?php
declare(strict_types=1);

namespace App\Domain\PickerWizard;


use App\Domain\PcParts\PcPart;

class Stage
{
    private $config;

    public function __construct()
    {
        $this->readConfig();
    }

    public function buildDummyPart(int $stageIdx): PcPart
    {
        $stageClassName = $this->config[$stageIdx];

        $class = PcPart::ENTITIES_NAMESPACE . $stageClassName;
        return new $class;
    }

    private function readConfig(): array
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    }
}
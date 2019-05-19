<?php
declare(strict_types=1);

namespace App\Parser;

use App\Domain\PcParts\Entities\CPU;
use App\Domain\PcParts\Entities\PcCase;
use App\Domain\PcParts\Entities\PowerSupply;
use App\Domain\PcParts\Entities\RAM;
use App\Domain\PcParts\Entities\Specification;
use App\Domain\PcParts\Entities\VideoCard;
use App\Domain\PcParts\PcPart;
use App\Domain\PickerWizard\Stage;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Fixer implements LoggerAwareInterface
{
    /**@var LoggerInterface $logger*/
    private $logger;

    public static function instantiate(): self
    {
        return new self();
    }


    public function addMemorySizeToAllRamModels(): void
    {
        $ramCollection = (new RAM())->newQuery();

        $ramCollection->each(function(RAM $ram) {
            if (empty($ram->getAttribute('memorySize'))) {
                $ram->setAttribute('memorySize', $ram->getSize());
                $ram->save();

                $this->logger->info("Processed ram #{$ram->getKey()}");
            }
        }, 100);
    }

    public function addWattageToAllPowerSupplyModels(): void
    {
        $supplyCollection = (new PowerSupply())->newQuery();

        $supplyCollection->each(function(PowerSupply $supply) {
            if (empty($supply->getAttribute('wattage'))) {
                $supply->setAttribute('wattage', (int)$supply->getAttribute('specifications')['Wattage']['value']);
                $supply->save();

                $this->logger->info("Processed supply #{$supply->getKey()}");
            }
        }, 100);
    }

    public function addMaxSupportedVideoCardLengthToAllCaseModels(): void
    {
        $caseCollection = (new PcCase())->newQuery();

        $caseCollection->each(function(PcCase $case) {
            if (empty($case->getAttribute('maxVideoCardLength'))) {
                $length = isset($case->getAttribute('specifications')['Maximum Video Card Length'])
                    ? (int)$case->getAttribute('specifications')['Maximum Video Card Length']['value']
                    : random_int(100, 500);
                    $case->setAttribute('maxVideoCardLength', $length);

                $case->save();

                $this->logger->info("Processed case #{$case->getKey()}");
            }
        }, 100);
    }

    public function normalizeCPUFrequencies(): void
    {
        $cpuCollection = (new CPU())->newQuery();

        $cpuCollection->each(function(CPU $cpu) {
            $specs = $cpu->getAttribute('specifications');
            $changed = false;

            if (!isset($specs['Boost Clock'])) {
                $specs = array_replace_recursive($specs, [
                    'Boost Clock' => [
                        'key' => 'Boost Clock',
                        'value' => $specs['Core Clock']['value']
                    ]
                ]);
                $changed = true;
            }

            if ($changed) {
                $cpu->setAttribute('specifications', $specs);
                $cpu->save();
            }
        }, 100);
    }

    public function normalizeGraphicsFrequencies(): void
    {
        $gpuCollection = (new VideoCard())->newQuery();

        $gpuCollection->each(function(VideoCard $gpu) {
            $specs = $gpu->getAttribute('specifications');
            $changed = false;
            if (!isset($specs['Core Clock'])) {
                $randomMhz = random_int(0, 600);
                $specs = array_replace_recursive($specs, [
                    'Core Clock' => [
                        'key' => 'Core Clock',
                        'value' => '1' . ($randomMhz ? ".$randomMhz" : '') . ' GHz'
                    ]
                ]);
                $changed = true;
            }

            if (!isset($specs['Boost Clock'])) {
                $specs = array_replace_recursive($specs, [
                    'Boost Clock' => [
                        'key' => 'Boost Clock',
                        'value' => $specs['Core Clock']['value']
                    ]
                ]);
                $changed = true;
            }

            if ($changed) {
                $gpu->setAttribute('specifications', $specs);
                $gpu->save();
            }
        }, 100);
    }

    public function getAllSpecificationsKeys(): array
    {
        $stageIdx = 0;
        $specifications = [];
        $stage = new Stage($stageIdx);
        while (!$stage->allStagesPassed()) {
            $model = $stage->buildDummyPart();
            $model = $model->newQuery()->first();

            $specifications = array_merge($specifications, array_keys($model->getAttribute('specifications')));
            $stage = new Stage(++$stageIdx);
        }

        return array_unique($specifications);
    }

    public function createSpecificationTranslations(array $translations): void
    {
//        (new Specification())->newQuery()->where('1', '=', '1')->delete();

        foreach ($translations as $key => $translation) {
            $specification = new Specification();
            $specification->key = $key;
            $specification->translation = $translation;
            $specification->save();
        }
    }

    public function addNumberPriceToAllModels(): void
    {
        $stageIdx = 0;
        $stage = new Stage($stageIdx);
        while (!$stage->allStagesPassed()) {
            $model = $stage->buildDummyPart();
            $model = $model->newQuery();

            $model->each(function (PcPart $part) {
                $part->priceNumber = empty($part->price) ? 0 : (float)str_replace('$', '', $part->price);
                $part->save();
            }, 100);

            $stage = new Stage(++$stageIdx);
        }
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
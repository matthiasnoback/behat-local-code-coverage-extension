<?php
declare(strict_types=1);

namespace BehatLocalCodeCoverage;

use Behat\Behat\EventDispatcher\Event\ScenarioLikeTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use LiveCodeCoverage\CodeCoverageFactory;
use LiveCodeCoverage\Storage;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LocalCodeCoverageListener implements EventSubscriberInterface
{
    /**
     * @var CodeCoverage
     */
    private $coverage;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var bool
     */
    private $coverageEnabled = false;

    public function __construct($phpunitXmlPath, $targetDirectory)
    {
        $this->coverage = CodeCoverageFactory::createFromPhpUnitConfiguration($phpunitXmlPath);
        $this->targetDirectory = $targetDirectory;
    }

    public static function getSubscribedEvents()
    {
        return [
            SuiteTested::BEFORE => 'beforeSuite',
            ScenarioTested::BEFORE => 'beforeScenario',
            ScenarioTested::AFTER => 'afterScenario',
            SuiteTested::AFTER => 'afterSuite'
        ];
    }

    public function beforeSuite(SuiteTested $event)
    {
        $this->coverageEnabled = $event->getSuite()->hasSetting('local_coverage_enabled')
            && (bool)$event->getSuite()->getSetting('local_coverage_enabled');
    }

    public function beforeScenario(ScenarioLikeTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        $coverageId = $event->getFeature()->getFile() . ':' . $event->getScenario()->getLine();

        $this->coverage->start($coverageId);
    }

    public function afterScenario(ScenarioLikeTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        $this->coverage->stop();
    }

    public function afterSuite(AfterSuiteTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        Storage::storeCodeCoverage($this->coverage, $this->targetDirectory, $event->getSuite()->getName());

        $this->reset();
    }

    private function reset()
    {
        $this->coverage = null;
        $this->coverageEnabled = false;
    }
}

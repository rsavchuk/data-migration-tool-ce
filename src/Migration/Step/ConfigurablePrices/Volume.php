<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Step\ConfigurablePrices;

use Migration\App\Step\AbstractVolume;
use Migration\Logger\Logger;
use Migration\Reader\MapInterface;
use Migration\Resource;
use Migration\App\ProgressBar;

class Volume extends AbstractVolume
{
    /**
     * @var Resource\Source
     */
    protected $source;

    /**
     * @var Resource\Destination
     */
    protected $destination;

    /**
     * LogLevelProcessor instance
     *
     * @var ProgressBar\LogLevelProcessor
     */
    protected $progressBar;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Logger $logger
     * @param Resource\Source $source
     * @param Resource\Destination $destination
     * @param ProgressBar\LogLevelProcessor $progressBar
     * @param Helper $helper
     */
    public function __construct(
        Logger $logger,
        Resource\Source $source,
        Resource\Destination $destination,
        ProgressBar\LogLevelProcessor $progressBar,
        Helper $helper
    ) {
        $this->source = $source;
        $this->destination = $destination;
        $this->progressBar = $progressBar;
        $this->helper = $helper;
    }

    /**
     * @return bool
     */
    public function perform()
    {
        $documents = $this->helper->getDocumentList();
        $this->progressBar->start(1);
        $sourceRecordsCount = $this->source->getRecordsCount($documents[MapInterface::TYPE_SOURCE]);
        $oldDestinationRecordsCount = $this->helper->getDestinationRecordsCount();
        $newDestinationRecordsCount = $this->destination->getRecordsCount($documents[MapInterface::TYPE_DEST])
            - $oldDestinationRecordsCount;
        if ($sourceRecordsCount != $newDestinationRecordsCount) {
            $message = 'Mismatch of entities in the document: ' . $documents[MapInterface::TYPE_DEST];
            $this->logger->error($message);
        }
        $this->progressBar->finish();
        return $this->checkForErrors();
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Cms\Block\Block;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContent\Model\ContentProcessor;

/**
 * Observe cms_block_save_after event and run processing relation between cms block content and media asset
 */
class CmsBlock implements ObserverInterface
{
    private const CONTENT_TYPE = 'cms_block';

    /**
     * @var ContentProcessor
     */
    private $contentProcessor;

    /**
     * @var array
     */
    private $fields;

    /**
     * CmsBlock constructor.
     *
     * @param ContentProcessor $contentProcessor
     * @param array $fields
     */
    public function __construct(ContentProcessor $contentProcessor, array $fields)
    {
        $this->contentProcessor = $contentProcessor;
        $this->fields = $fields;
    }

    /**
     * Get changed content data matches to the search interest and run relation processor.
     *
     * @param Observer $observer
     *
     * @throws IntegrationException
     */
    public function execute(Observer $observer): void
    {
        $content = [];
        /** @var Block $cmsBlock */
        $cmsBlock = $observer->getEvent()->getObject();
        foreach ($this->fields as $field) {
            if ($cmsBlock->dataHasChangedFor($field)) {
                $content[$field] = $cmsBlock->getData($field);
            }
        }

        if (!empty($content)) {
            $this->contentProcessor->execute((string)$cmsBlock->getId(), $content, self::CONTENT_TYPE);
        }
    }
}

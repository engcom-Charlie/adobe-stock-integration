<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaContent\Observer;

use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContent\Model\ContentProcessor;

/**
 * Observe the catalog_category_save_after event and run processing relation between category content and media asset.
 */
class CatalogCategory implements ObserverInterface
{
    private const CONTENT_TYPE = 'catalog_category';

    /**
     * @var ContentProcessor
     */
    private $contentProcessor;

    /**
     * @var array
     */
    private $fields;

    /**
     * CatalogCategory constructor.
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
        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');
        foreach ($this->fields as $field) {
            if ($category->dataHasChangedFor($field)) {
                $content[$field] = $category->getData($field);
            }
        }

        if (!empty($content)) {
            $this->contentProcessor->execute((string)$category->getId(), $content, self::CONTENT_TYPE);
        }
    }
}

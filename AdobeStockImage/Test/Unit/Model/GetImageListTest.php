<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\DocumentToAsset;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetSearchResultsInterfaceFactory;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImage\Model\GetImageList;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;

/**
 * Test for GetImageList service
 */
class GetImageListTest extends TestCase
{
    /**
     * @var GetImageList
     */
    private $model;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientMock;

    /**
     * @var AssetSearchResultsInterfaceFactory|MockObject
     */
    private $searchResultFactoryMock;

    /**
     * @var DocumentToAsset|MockObject
     */
    private $converterMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var MockObject
     */
    private $filterBuilderMock;

    /**
     * @var MockObject
     */
    private $filterGroupBuilderMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->searchResultFactoryMock = $this->createMock(AssetSearchResultsInterfaceFactory::class);
        $this->converterMock = $this->createMock(DocumentToAsset::class);
        $this->urlMock = $this->createMock(UrlInterface::class);
        $this->filterBuilderMock = $this->createMock(FilterBuilder::class);
        $this->filterGroupBuilderMock = $this->createMock(FilterGroupBuilder::class);

        $this->model = (new ObjectManager($this))->getObject(
            GetImageList::class,
            [
                'client' => $this->clientMock,
                'searchResultFactory' => $this->searchResultFactoryMock,
                'url' => $this->urlMock,
                'documentToAsset' => $this->converterMock,
                'filterBuilder' => $this->filterBuilderMock,
                'filterGroupBuilder' => $this->filterGroupBuilderMock,
                'defaultFilters' => [
                    'filter1' => ['type' => 'content_type_filter', 'condition' => 'or', 'field' => 'illustration'],
                    'filter2' => ['type' => 'content_type_filter', 'condition' => 'or', 'field' => 'photo'],
                    ]
            ]
        );
    }

    /**
     * Test execute method
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testExecute()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $filter = $this->createMock(\Magento\Framework\Api\Filter::class);

        $documentMock = $this->createMock(DocumentInterface::class);
        $documentSearchResults = $this->createMock(SearchResultInterface::class);
        $documentSearchResults->expects($this->once())->method('getItems')->willReturn([$documentMock]);
        $documentSearchResults->expects($this->once())->method('getTotalCount')->willReturn(1);
        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($searchCriteriaMock)
            ->willReturn($documentSearchResults);
        $this->filterBuilderMock->expects($this->atLeast(2))
            ->method('setField')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->atLeast(2))
            ->method('setConditionType')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->atLeast(2))
            ->method('setValue')
            ->willReturn($this->filterBuilderMock);
        $this->filterBuilderMock->expects($this->atLeast(2))
            ->method('create')
            ->willReturn($filter);
        $this->filterGroupBuilderMock->expects($this->atLeast(1))
            ->method('setFilters')
            ->willReturn($this->filterGroupBuilderMock);

        $assetMock = $this->createMock(AssetInterface::class);

        $this->converterMock->expects($this->once())->method('convert')->with($documentMock)->willReturn($assetMock);

        $assetSearchResults = $this->createMock(AssetSearchResultsInterface::class);
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'data' => [
                        'items' => [$assetMock],
                        'total_count' => 1,
                    ],
                ]
            )
            ->willReturn($assetSearchResults);
        $this->assertEquals($assetSearchResults, $this->model->execute($searchCriteriaMock));
    }
}

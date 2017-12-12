<?php

namespace EzSystems\RepositoryForms\Tests\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\URL;
use EzSystems\RepositoryForms\Pagination\Pagerfanta\URLUsagesAdapter;
use PHPUnit\Framework\TestCase;

class URLUsagesAdapterTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\URLService|\PHPUnit_Framework_MockObject_MockObject */
    private $urlService;

    protected function setUp()
    {
        $this->urlService = $this->createMock(URLService::class);
    }

    public function testGetNbResults()
    {
        $url = $this->createMock(URL::class);

        $searchResults = new SearchResult([
            'searchHits' => [],
            'totalCount' => 10,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUsages')
            ->with($url, 0, 0)
            ->willReturn($searchResults);

        $adapter = new URLUsagesAdapter($url, $this->urlService);

        $this->assertEquals(
            $searchResults->totalCount,
            $adapter->getNbResults()
        );
    }

    public function testGetSlice()
    {
        $url = $this->createMock(URL::class);
        $offset = 10;
        $limit = 25;

        $searchResults = new SearchResult([
            'searchHits' => [
                $this->createMock(SearchHit::class),
                $this->createMock(SearchHit::class),
                $this->createMock(SearchHit::class),
            ],
            'totalCount' => 13,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUsages')
            ->with($url, $offset, $limit)
            ->willReturn($searchResults);

        $adapter = new URLUsagesAdapter($url, $this->urlService);

        $this->assertEquals(
            $searchResults->searchHits,
            $adapter->getSlice($offset, $limit)
        );
    }
}

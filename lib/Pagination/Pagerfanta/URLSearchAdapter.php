<?php

namespace EzSystems\RepositoryForms\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\URLQuery;
use Pagerfanta\Adapter\AdapterInterface;

class URLSearchAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\URL\URLQuery
     */
    private $query;

    /**
     * @var \eZ\Publish\API\Repository\URLService
     */
    private $urlService;

    /**
     * UrlSearchAdapter constructor.
     *
     * @param \eZ\Publish\API\Repository\Values\URL\URLQuery $query
     * @param \eZ\Publish\API\Repository\URLService $urlService
     */
    public function __construct(URLQuery $query, URLService $urlService)
    {
        $this->query = $query;
        $this->urlService = $urlService;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $query = clone $this->query;
        $query->offset = 0;
        $query->limit = 0;

        return $this->urlService->findUrls($query)->totalCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;

        return $this->urlService->findUrls($query)->items;
    }
}

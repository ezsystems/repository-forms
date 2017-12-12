<?php

namespace EzSystems\RepositoryForms\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\URL;
use Pagerfanta\Adapter\AdapterInterface;

class URLUsagesAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\URLService
     */
    private $urlService;

    /**
     * @var \eZ\Publish\API\Repository\Values\URL\URL
     */
    private $url;

    /**
     * UrlUsagesAdapter constructor.
     *
     * @param \eZ\Publish\API\Repository\Values\URL\URL $url
     * @param \eZ\Publish\API\Repository\URLService $urlService
     */
    public function __construct(URL $url, URLService $urlService)
    {
        $this->urlService = $urlService;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->urlService->findUsages($this->url, 0, 0)->totalCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->urlService->findUsages($this->url, $offset, $length)->items;
    }
}

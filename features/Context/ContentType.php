<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use PHPUnit_Framework_Assert as Assertion;

class ContentType extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @Given /^there is a Content Type "([^"]*)" with the id "([^"]*)"$/
     */
    public function thereIsAContentTypeWithId($contentTypeIdentifier, $id)
    {
        try {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
            Assertion::assertEquals($id, $contentType->id);
        } catch (NotFoundException $e) {
            Assertion::fail("No ContentType with the identifier '$contentTypeIdentifier'' could be found.");
        }
    }
}

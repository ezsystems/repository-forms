<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Exception;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use EzSystems\PlatformBehatBundle\Context\RepositoryContext;
use PHPUnit_Framework_Assert as Assertion;

final class ContentType extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use RepositoryContext;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * Current content type within this context.
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    private $currentContentType;

    /**
     * @injectService $repository @ezpublish.api.repository
     * @injectService $contentTypeService @ezpublish.api.service.content_type
     */
    public function __construct(Repository $repository, ContentTypeService $contentTypeService)
    {
        $this->setRepository($repository);
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
            Assertion::fail("No ContentType with the identifier '$contentTypeIdentifier' could be found.");
        }
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     *
     * @throws \Exception if no current content type has been defined in the context
     */
    public function getCurrentContentType()
    {
        if ($this->currentContentType === null) {
            throw new Exception('No current content type has been defined in the context');
        }

        return $this->currentContentType;
    }

    public function createContentType(ContentTypeCreateStruct $struct)
    {
        if (!isset($struct->mainLanguageCode)) {
            $struct->mainLanguageCode = 'eng-GB';
        };
        if (!isset($struct->names)) {
            $struct->names = ['eng-GB' => $struct->identifier];
        }

        $this->contentTypeService->publishContentTypeDraft(
            $this->contentTypeService->createContentType(
                $struct,
                [$this->contentTypeService->loadContentTypeGroupByIdentifier('Content')]
            )
        );

        $this->currentContentType = $this->contentTypeService->loadContentTypeByIdentifier($struct->identifier);
    }

    /**
     * Creates a new content type create struct. If the identifier is not specified, a custom one is given.
     *
     * @return ContentTypeCreateStruct
     */
    public function newContentTypeCreateStruct($identifier = null)
    {
        return $this->contentTypeService->newContentTypeCreateStruct(
            $identifier ?: $identifier = 'content_type_' . uniqid()
        );
    }

    public function updateFieldDefinition($identifier, FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct)
    {
        $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($this->currentContentType);

        $this->contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $this->currentContentType->getFieldDefinition($identifier),
            $fieldDefinitionUpdateStruct
        );

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $this->currentContentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $this->currentContentType->identifier
        );
    }
}

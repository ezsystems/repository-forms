<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;

class ContentTypeCreateData extends ContentTypeCreateStruct
{
    /** @var int */
    public $contentTypeGroupId;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $fieldTypeSelection;

    /**
     * Holds the collection of field definitions.
     *
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct[]
     */
    public $fieldDefinitions = [];

    public function __construct(int $contentTypeGroupId)
    {
        $this->contentTypeGroupId = $contentTypeGroupId;
    }

    public function getContentTypeGroupId(): int
    {
        return $this->contentTypeGroupId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getFieldTypeSelection(): ?string
    {
        return $this->fieldTypeSelection;
    }

    public function setFieldTypeSelection($fieldTypeSelection): void
    {
        $this->fieldTypeSelection = $fieldTypeSelection;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldDefinition(FieldDefinitionCreateStruct $fieldDef): void
    {
        $this->fieldDefinitions[] = $fieldDef;
    }
}

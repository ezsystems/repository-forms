<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Field;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\User\UserCreateData;
use EzSystems\RepositoryForms\UserRegister\RegistrationContentTypeLoader;
use EzSystems\RepositoryForms\UserRegister\RegistrationGroupLoader;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for user registration / creation.
 */
class UserRegisterMapper
{
    /**
     * @var RegistrationContentTypeLoader
     */
    private $contentTypeLoader;

    /**
     * @var RegistrationGroupLoader
     */
    private $parentGroupLoader;

    /**
     * @var array
     */
    private $params;

    public function __construct(RegistrationContentTypeLoader $contentTypeLoader, RegistrationGroupLoader $registrationGroupLoader)
    {
        $this->contentTypeLoader = $contentTypeLoader;
        $this->parentGroupLoader = $registrationGroupLoader;
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @return UserCreateData
     */
    public function mapToFormData()
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        $contentType = $this->contentTypeLoader->loadContentType();

        $data = new UserCreateData([
            'contentType' => $contentType,
            'mainLanguageCode' => $this->params['language'],
        ]);
        $data->addParentGroup($this->parentGroupLoader->loadGroup());

        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldDef->identifier,
                    'languageCode' => $this->params['language'],
                ]),
                'value' => $fieldDef->defaultValue,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('language');
    }
}

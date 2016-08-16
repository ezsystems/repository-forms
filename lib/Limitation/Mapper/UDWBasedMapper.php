<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\DataTransformer\UDWBasedValueTransformer;
use EzSystems\RepositoryForms\Limitation\LimitationFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

/**
 * Base class for mappers based on Universal Discovery Widget.
 */
class UDWBasedMapper implements LimitationFormMapperInterface
{
    /**
     * Form template to use.
     *
     * @var string
     */
    private $template;

    public function setFormTemplate($template)
    {
        $this->template = $template;
    }

    public function getFormTemplate()
    {
        return $this->template;
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
        $form->add(
            // Creating from FormBuilder as we need to add a DataTransformer.
            $form->getConfig()->getFormFactory()->createBuilder()
                ->create('limitationValues', HiddenType::class, [
                    'required' => false,
                    'label' => $data->getIdentifier(),
                ])
                ->addModelTransformer(new UDWBasedValueTransformer())
                // Deactivate auto-initialize as we're not on the root form.
                ->setAutoInitialize(false)->getForm()
        );
    }

    public function filterLimitationValues(Limitation $limitation)
    {
    }
}

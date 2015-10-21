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
use EzSystems\RepositoryForms\Limitation\LimitationFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class NullLimitationMapper implements LimitationFormMapperInterface
{
    /**
     * @var string
     */
    private $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data)
    {
    }

    public function getFormTemplate()
    {
        return $this->template;
    }

    public function filterLimitationValues(Limitation $limitation)
    {
    }
}

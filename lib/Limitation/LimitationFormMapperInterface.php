<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;
use Symfony\Component\Form\FormInterface;

interface LimitationFormMapperInterface
{
    public function mapLimitationForm(FormInterface $form, Limitation $data);
}

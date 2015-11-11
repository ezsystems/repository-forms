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
use Symfony\Component\Translation\TranslatorInterface;

class GroupLimitationMapper extends MultipleSelectionBasedMapper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function getSelectionChoices()
    {
        return [
            1 => $this->translator->trans('policy.limitation.group.self', [], 'ezrepoforms_role'),
        ];
    }
}

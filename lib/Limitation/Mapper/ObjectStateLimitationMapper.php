<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation\Mapper;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;

class ObjectStateLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /**
     * @var ObjectStateService
     */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    protected function getSelectionChoices()
    {
        $choices = [];
        foreach ($this->objectStateService->loadObjectStateGroups() as $group) {
            foreach ($this->objectStateService->loadObjectStates($group) as $state) {
                $choices[$state->id] = $this->getObjectStateLabel($state);
            }
        }

        return $choices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];

        foreach ($limitation->limitationValues as $stateId) {
            $values[] = $this->getObjectStateLabel(
                $this->objectStateService->loadObjectState($stateId)
            );
        }

        return $values;
    }

    protected function getObjectStateLabel(ObjectState $state)
    {
        $groupName = $state
            ->getObjectStateGroup()
            ->getName($state->defaultLanguageCode);

        $stateName = $state->getName($state->defaultLanguageCode);

        return $groupName . ':' . $stateName;
    }
}

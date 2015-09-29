<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\User\Policy;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Role\PolicyCreateData;
use EzSystems\RepositoryForms\Data\Role\PolicyUpdateData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PolicyMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param ValueObject|\eZ\Publish\API\Repository\Values\User\Policy $policy
     * @param array $params
     *
     * @return PolicyUpdateData|PolicyCreateData
     */
    public function mapToFormData(ValueObject $policy, array $params = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $params = $resolver->resolve($params);

        if (!$this->isPolicyNew($policy)) {
            $data = new PolicyUpdateData([
                'policy' => $policy,
                'roleDraft' => $params['roleDraft'],
                'moduleFunction' => "{$policy->module}|{$policy->function}",
            ]);
        } else {
            $data = new PolicyCreateData([
                'policy' => $policy,
                'roleDraft' => $params['roleDraft'],
            ]);
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired('roleDraft')
            ->setAllowedTypes('roleDraft', '\eZ\Publish\API\Repository\Values\User\RoleDraft');
    }

    private function isPolicyNew(Policy $policy)
    {
        return $policy->id === null;
    }
}

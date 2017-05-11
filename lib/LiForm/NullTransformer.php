<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\LiForm;

use Limenius\Liform\Transformer\AbstractTransformer;
use Symfony\Component\Form\FormInterface;

class NullTransformer extends AbstractTransformer
{
    /**
     * transform.
     *
     * @param FormInterface $form
     * @param array         $extensions
     * @param string|null   $widget
     *
     * @return array
     */
    public function transform(FormInterface $form, $extensions = [], $widget = null)
    {
        $schema = null;
    }
}

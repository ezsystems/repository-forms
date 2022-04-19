<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\RepositoryForms\Form\EventListener;

use Symfony\Component\Form\FormEvent;

class SelectionMultilingualOptionsDataListener
{
    /** @var string */
    protected $languageCode;

    public function __construct(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function setLanguageOptions(FormEvent $event): void
    {
        $data = $event->getData();
        $event->setData($data[$this->languageCode] ?? []);
    }
}

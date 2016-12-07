<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Translation;

use eZ\Publish\API\Repository\Values\Content\Location;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;

/**
 * Generates translation strings for sort options (field and order).
 */
class SortingTranslationExtractor implements ExtractorInterface
{
    private $defaultTranslations = [
        1 => 'Location path',
        2 => 'Publication date',
        3 => 'Modification date',
        4 => 'Section',
        5 => 'Location depth',
        6 => 'ContentType identifier',
        7 => 'ContentType name',
        8 => 'Location priority',
        9 => 'Content name',
    ];

    private $domain = 'ezrepoforms_content_type';

    public function extract()
    {
        $catalogue = new MessageCatalogue();
        $locationClass = new \ReflectionClass(Location::class);

        $sortConstants = array_filter(
            $locationClass->getConstants(),
            function ($value, $key) {
                return is_scalar($value) && strtolower(substr($key, 0, 11)) === 'sort_field_';
            },
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($sortConstants as $sortId) {
            if (!isset($this->defaultTranslations[$sortId])) {
                continue;
            }
            $catalogue->add(
                $this->createMessage(
                    'content_type.sort_field.' . $sortId,
                    $this->defaultTranslations[$sortId],
                    Location::class
                )
            );
        }

        $catalogue->add($this->createMessage('content_type.sort_order.0', 'Descending', Location::class));
        $catalogue->add($this->createMessage('content_type.sort_order.1', 'Ascending', Location::class));

        return $catalogue;
    }

    /**
     * @param string $id The translation key
     * @param string $desc Human readable translation / hint
     * @param string $source The translation's source
     *
     * @return Message
     */
    private function createMessage($id, $desc, $source)
    {
        $message = new Message\XliffMessage($id, $this->domain);
        $message->addSource(new FileSource($source));
        $message->setMeaning($desc);
        $message->setLocaleString($desc);
        $message->addNote('key: ' . $id);

        return $message;
    }
}

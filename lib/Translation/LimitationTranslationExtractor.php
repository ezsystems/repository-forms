<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\ExtractorInterface;

/**
 * Generates translation strings for limitation types.
 */
class LimitationTranslationExtractor implements ExtractorInterface
{
    const MESSAGE_DOMAIN = 'ezrepoforms_policies';
    const MESSAGE_ID_PREFIX = 'policy.limitation.identifier.';

    /**
     * @var array
     */
    private $policyMap;

    public function __construct(array $policyMap)
    {
        $this->policyMap = $policyMap;
    }

    public function extract()
    {
        $catalogue = new MessageCatalogue();

        foreach ($this->getLimitationTypes() as $limitationType) {
            $id = self::MESSAGE_ID_PREFIX . strtolower($limitationType);

            $message = new Message\XliffMessage($id, self::MESSAGE_DOMAIN);
            $message->setNew(false);
            $message->setMeaning($limitationType);
            $message->setDesc($limitationType);
            $message->setLocaleString($limitationType);
            $message->addNote('key: ' . $id);

            $catalogue->add($message);
        }

        return $catalogue;
    }

    public static function identifierToLabel(string $limitationIdentifier): string
    {
        return self::MESSAGE_ID_PREFIX . strtolower($limitationIdentifier);
    }

    /**
     * Returns all known limitation types.
     *
     * @return array
     */
    private function getLimitationTypes()
    {
        $limitationTypes = [];
        foreach ($this->policyMap as $module) {
            foreach ($module as $policy) {
                if (null === $policy) {
                    continue;
                }

                foreach (array_keys($policy) as $limitationType) {
                    if (!\in_array($limitationType, $limitationTypes)) {
                        $limitationTypes[] = $limitationType;
                    }
                }
            }
        }

        return $limitationTypes;
    }
}

<?php

namespace MPOS\Helpers;

use MPOS\Enums\LanguagesEnum;
use MPOS\Exceptions\MPOSException;
use Symfony\Component\Translation\Translator;
use League\Container\ContainerAwareTrait;

class TranslationsHelper
{
    use ContainerAwareTrait;

    /** @var ConfigHelper */
    private $configHelper;

    /** @var Translator */
    private $translator;

    public function __construct(ConfigHelper $configHelper, Translator $translator)
    {
        $this->configHelper = $configHelper;
        $this->translator = $translator;
    }

    /**
     * @param string $lang
     * @param string $key
     * @return string
     * @throws MPOSException
     */
    public function translate(string $lang, string $key): string
    {
        $locale = LanguagesEnum::getLocaleByLanguage($lang);
        $description = $this->convertKeyToDescription($key);

        return $this->translator->trans($key, [], null, $locale) ?: $description;
    }

    private function convertKeyToDescription(string $key): string
    {
        return trim(str_replace(':', ' ', $key));
    }
}

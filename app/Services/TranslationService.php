<?php

namespace App\Services;

use Barryvdh\TranslationManager\Manager;
use Barryvdh\TranslationManager\Models\Translation;

//write comment that this class is deprecated

/**
 * @deprecated
 * Use TranslatorService instead
 */
class TranslationService
{
    public const LANG_PL = 'pl';

    public const LANG_EN = 'en';

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var string
     */
    private static $group = '_json';

    private static $publish = true;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $group
     *
     * @return TranslationService
     */
    public function setGroup(string $group): self
    {
        self::$group = $group;

        return $this;
    }

    /**
     * @param string $keyPl
     * @param string $versionEn
     * @param string $group
     *
     * @return TranslationService
     */
    public function addLangVersion(string $keyPl = null, string $versionEn = null, string $group = null): self
    {
        $this->addKey($keyPl, $group);

        $group = $group ?: self::$group;

        if (in_array($group, $this->manager->getConfig('exclude_groups'))) {
            return $this;
        }
        $translation = Translation::firstOrNew([
                    'locale' => self::LANG_EN,
                    'group' => $group,
                    'key' => $keyPl,
        ]);
        if ($translation->value === $versionEn) {
            return $this;
        }
        $translation->value = (string) $versionEn ?: null;
        $translation->status = Translation::STATUS_CHANGED;
        $translation->save();
        self::$publish = false;

        return $this;
    }

    /**
     * @param string $keyPl
     * @param string $locale
     * @param string $locale_value
     * @param string $group
     *
     * @return TranslationService
     */
    public function addLangLocaleVersion(string $keyPl = null, string $locale = null, $locale_value = null, string $group = null, int $object_id = null, string $object_table = null, string $object_column = null): self
    {
        if (! $keyPl) {
            return $this;
        }

        $this->addKey($keyPl, $group, $object_id, $object_table, $object_column);

        $group = $group ?: self::$group;

        if (in_array($group, $this->manager->getConfig('exclude_groups'))) {
            return $this;
        }
        $translation = Translation::firstOrNew(
            [
                    'locale' => $locale,
                    'group' => $group,
                    'key' => $keyPl,
                    'object_id' => $object_id,
                    'object_table' => $object_table,
                    'object_column' => $object_column,
                ]
        );
        if ($translation->value === $locale_value) {
            return $this;
        }
        $translation->value = (string) $locale_value ?: null;
        $translation->status = Translation::STATUS_CHANGED;
        $translation->save();

        self::$publish = false;

        return $this;
    }

    /**
     * @param string $keyPl
     * @param string $group
     * @param string $lang
     *
     * @return TranslationService
     */
    public function removeLangVersion(string $keyPl = null, string $group = null, string $lang = self::LANG_EN): self
    {
        if (empty($keyPl)) {
            return $this;
        }
        $group = $group ?: self::$group;

        if (in_array($group, $this->manager->getConfig('exclude_groups')) || ! $this->manager->getConfig('delete_enabled')) {
            return $this;
        }

        Translation::where('group', $group)->where('key', $keyPl)->where('locale', self::LANG_EN)->delete();
        self::$publish = false;

        return $this;
    }

    /**
     * @param string $replaceKeyPl
     * @param string $newKeyPl
     * @param string $group
     *
     * @return TranslationService
     */
    public function updateKey(string $replaceKeyPl = null, string $newKeyPl = null, string $group = null, int $object_id = null, string $object_table = null, string $object_column = null): self
    {
        if (empty($replaceKeyPl) || empty($newKeyPl)) {
            return $this;
        }
        $group = $group ?: self::$group;
        if (in_array($group, $this->manager->getConfig('exclude_groups'))) {
            return $this;
        }

        Translation::where('group', $group)->where('key', $replaceKeyPl)->update(['key' => $newKeyPl, 'object_id' => $object_id, 'object_table' => $object_table, 'object_column' => $object_column]);
        self::$publish = false;

        return $this;
    }

    /**
     * @param string $keyPl
     * @param string $group
     *
     * @return TranslationService
     */
    public function addKey(string $keyPl = null, string $group = null, int $object_id = null, string $object_table = null, string $object_column = null): self
    {
        if (empty($keyPl)) {
            return $this;
        }
        $group = $group ?: self::$group;

        $this->manager->missingKey('*', $group, $keyPl, $object_id, $object_table, $object_column);
        self::$publish = false;

        return $this;
    }

    /**
     * @param string $keyPl
     * @param string $group
     *
     * @return TranslationService
     */
    public function removeKey(string $keyPl = null, string $group = null): self
    {
        if (empty($keyPl)) {
            return $this;
        }
        $group = $group ?: self::$group;

        if (in_array($group, $this->manager->getConfig('exclude_groups')) || ! $this->manager->getConfig('delete_enabled')) {
            return $this;
        }

        Translation::where('group', $group)->where('key', $keyPl)->delete();
        self::$publish = false;

        return $this;
    }

    /**
     * @param string $group
     * @param bool   $force
     *
     * @return TranslationService
     */
    public function publish(string $group = null, bool $force = false): self
    {
        $group = $group ?: self::$group;
        $json = false;

        if ('_json' === $group) {
            $json = true;
        }

        if (! self::$publish || $force) {
            //$this->manager->exportTranslations($group, $json);
        }

        return $this;
    }
}

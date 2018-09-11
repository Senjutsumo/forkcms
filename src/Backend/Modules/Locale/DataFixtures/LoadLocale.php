<?php

namespace Backend\Modules\Locale\DataFixtures;

class LoadLocale
{
    public function load(\SpoonDatabase $database): void
    {
        $database->insert(
            'locale',
            [
                self::backendCoreLabelData(),
                self::backendCoreErrorData(),
                self::backendCoreMessageData(),
                self::backendCoreActionData(),
                self::backendModuleLabelData(),
                self::backendModuleErrorData(),
                self::backendModuleMessageData(),
                self::backendModuleActionData(),
                self::frontendCoreLabelData(),
                self::frontendCoreErrorData(),
                self::frontendCoreMessageData(),
                self::frontendCoreActionData(),
            ]
        );
    }

    public static function backendCoreLabelData()
    {
        return self::getLocaleRecord(
            9000,
            'Backend',
            'Core',
            'lbl',
            'BackendCoreLabel',
            'backend core label value'
        );
    }

    public static function backendCoreErrorData()
    {
        return self::getLocaleRecord(
            9001,
            'Backend',
            'Core',
            'err',
            'BackendCoreError',
            'backend core error value'
        );
    }

    public static function backendCoreMessageData()
    {
        return self::getLocaleRecord(
            9002,
            'Backend',
            'Core',
            'msg',
            'BackendCoreMessage',
            'backend core message value'
        );
    }

    public static function backendCoreActionData()
    {
        return self::getLocaleRecord(
            9003,
            'Backend',
            'Core',
            'act',
            'BackendCoreAction',
            'backend core action value'
        );
    }

    public static function backendModuleLabelData()
    {
        return self::getLocaleRecord(
            9004,
            'Backend',
            'Locale',
            'lbl',
            'BackendLocaleLabel',
            'backend locale label value'
        );
    }

    public static function backendModuleErrorData()
    {
        return self::getLocaleRecord(
            9005,
            'Backend',
            'Locale',
            'err',
            'BackendLocaleError',
            'backend module error value'
        );
    }

    public static function backendModuleMessageData()
    {
        return self::getLocaleRecord(
            9006,
            'Backend',
            'Locale',
            'msg',
            'BackendLocaleMessage',
            'backend module message value'
        );
    }

    public static function backendModuleActionData()
    {
        return self::getLocaleRecord(
            9007,
            'Backend',
            'Locale',
            'act',
            'BackendLocaleAction',
            'backend module action value'
        );
    }

    public static function frontendCoreLabelData()
    {
        return self::getLocaleRecord(
            9008,
            'Frontend',
            'Core',
            'lbl',
            'FrontendCoreLabel',
            'frontend core label value'
        );
    }

    public static function frontendCoreErrorData()
    {
        return self::getLocaleRecord(
            9009,
            'Frontend',
            'Core',
            'err',
            'FrontendCoreError',
            'frontend core error value'
        );
    }

    public static function frontendCoreMessageData()
    {
        return self::getLocaleRecord(
            9010,
            'Frontend',
            'Core',
            'msg',
            'FrontendCoreMessage',
            'frontend core message value'
        );
    }

    public static function frontendCoreActionData()
    {
        return self::getLocaleRecord(
            9011,
            'Frontend',
            'Core',
            'act',
            'FrontendCoreAction',
            'frontend core action value'
        );
    }

    public static function getLocaleRecord(
        int $id,
        string $application,
        string $module,
        string $type,
        string $name,
        string $value
    ): array {
        return [
            'id' => $id,
            'user_id' => 1,
            'language' => 'en',
            'application' => $application,
            'module' => $module,
            'type' => $type,
            'name' => $name,
            'value' => $value,
            'edited_on' => '2017-08-31 14:28:18'
        ];
    }
}

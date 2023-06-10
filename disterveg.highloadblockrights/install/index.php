<?php

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ModuleManager,
    Bitrix\Main\EventManager,
    Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

if (class_exists('Disterveg_highloadblockrights')) {
    return;
}

class Disterveg_highloadblockrights extends \CModule
{
    protected array $events = [
        'main' => [
            'OnUserTypeBuildList' => [
                '\Disterveg\HighloadblockRights\UserField\Types\HlBlockElementRightsType', 'getUserTypeDescription'
            ],
        ],
    ];

    public function __construct()
    {
        $this->MODULE_ID = 'disterveg.highloadblockrights';
        $this->MODULE_NAME = Loc::getMessage('DISTERVEG_HLBLOCK_RIGHTS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DISTERVEG_HLBLOCK_RIGHTS_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'disterveg';
        $this->PARTNER_URI = '';
        $this->setVersionData();
    }

    private function setVersionData()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    public function installFiles()
    {
        CopyDirFiles(
            __DIR__ . '/components/',
            Application::getDocumentRoot() . '/bitrix/components/',
            true,
            true
        );

        return true;
    }

    private function installAgents()
    {
        return true;
    }

    private function unInstallAgents()
    {
        return true;
    }

    public function installDB()
    {
        global $DB, $APPLICATION;

        $errors = $DB->runSQLBatch(
            __DIR__ . '/db/mysql/install.sql'
        );
        if ($errors !== false) {
            $APPLICATION->throwException(implode('', $errors));
            return false;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        return true;
    }

    public function installEvents()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->events as $module => $events) {
            foreach ($events as $eventCode => $callback) {
                $eventManager->registerEventHandler(
                    $module,
                    $eventCode,
                    $this->MODULE_ID,
                    $callback[0],
                    $callback[1]
                );
            }
        }

        return true;
    }

    public function unInstallDB(array $params = [])
    {
        global $APPLICATION, $DB;

        $errors = false;

        if (isset($params['savedata']) && !$params['savedata']) {
            $errors = $DB->runSQLBatch(
                __DIR__ . '/db/mysql/uninstall.sql'
            );
        }
        if ($errors !== false) {
            $APPLICATION->throwException(implode('', $errors));
            return false;
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);

        return true;
    }

    public function unInstallFiles()
    {
        DeleteDirFilesEx('/bitrix/components/' . $this->PARTNER_NAME . '/highloadblock.field.rights/');

        return true;
    }

    public function unInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->events as $module => $events) {
            foreach ($events as $eventCode => $callback) {
                $eventManager->unRegisterEventHandler(
                    $module,
                    $eventCode,
                    $this->MODULE_ID,
                    $callback[0],
                    $callback[1]
                );
            }
        }

        return true;
    }

    /**
     * Обязательные модули
     *
     * @return string[]
     */
    private function getRequiredModules(): array
    {
        /**
         * формат: [<moduleId> => <version>, ...]
         *
         * version = * - любая версия
         */
        return [
            'main' => '18.5.180',
            'highloadblock' => '*',
        ];
    }

    /**
     * Проверка на обязательные установленные модули
     *
     * @return bool
     */
    private function isRequiredModulesInstalled()
    {
        global $APPLICATION;

        foreach ($this->getRequiredModules() as $moduleId => $version) {
            if (!ModuleManager::isModuleInstalled($moduleId)) {
                $APPLICATION->ThrowException(
                    Loc::getMessage('DISTERVEG_HLBLOCK_RIGHTS_REQUIRED_MODULE_ERROR', ['#MODULE#' => $moduleId])
                );

                return false;
            } elseif ($version !== '*' && !CheckVersion(ModuleManager::getVersion($moduleId), $version)) {
                $APPLICATION->ThrowException(
                    Loc::getMessage(
                        'DISTERVEG_HLBLOCK_RIGHTS_MODULE_VERSION_ERROR',
                        ['#MODULE#' => $moduleId, '#VERSION#' => $version]
                    )
                );
                return false;
            }
        }

        return true;
    }

    public function doInstall()
    {
        if (!$this->isRequiredModulesInstalled()) {
            return false;
        }

        $this->installFiles();
        $this->installDB();
        $this->installEvents();
        $this->installAgents();

        $GLOBALS['APPLICATION']->includeAdminFile(
            Loc::getMessage('DISTERVEG_HLBLOCK_RIGHTS_INSTALL_TITLE'),
            __DIR__ . '/step1.php'
        );

        return true;
    }

    public function doUninstall()
    {
        global $APPLICATION;

        $request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
        $step = (int)$request->get('step') ?? 1;
        if ($step < 2) {
            $APPLICATION->includeAdminFile(
                Loc::getMessage('DISTERVEG_HLBLOCK_RIGHTS_UNINSTALL_TITLE'),
                __DIR__ . '/unstep1.php'
            );
        } elseif ($step == 2) {
            $params = [];
            if (!empty($request->get('savedata'))) {
                $params['savedata'] = $request->get('savedata') == 'Y';
            }
            $this->unInstallDB($params);
            $this->uninstallFiles();
            $this->unInstallEvents();
            $this->unInstallAgents();
            $APPLICATION->includeAdminFile(
                Loc::getMessage('DISTERVEG_HLBLOCK_RIGHTS_UNINSTALL_TITLE'),
                __DIR__ . '/unstep2.php'
            );
        }

        return true;
    }
}

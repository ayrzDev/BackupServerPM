<?php

namespace BackupTools\Ayrz;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{

    private static $getapi;

    public function onLoad(): void
    {
        self::$getapi = $this;
    }
    public function onEnable(): void
    {
      
        if (!file_exists($this->getServer()->getDataPath() . "backups/")) {
            mkdir($this->getServer()->getDataPath() . "backups/");
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("backup", new BackupCommand());
    }

    public static function getAPI()
    {
        return self::$getapi;
    }
}

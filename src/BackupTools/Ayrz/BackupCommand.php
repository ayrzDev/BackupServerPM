<?php

namespace BackupTools\Ayrz;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class BackupCommand extends Command
{

    public function __construct()
    {
        parent::__construct("backup");
        $this->setDescription("Sunucu Yedek Alır");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof ConsoleCommandSender) {
            $sender->sendMessage(TextFormat::RED . "Bu komut yalnızca konsoldan çalıştırılabilir.");
            return true;
        }

        $backupName = Main::getAPI()->getServer()->getDataPath() . "/backups";
        if (!is_dir($backupName)) {
            mkdir($backupName);
        }
        $task = new SaveTask($backupName);
        Main::getAPI()->getScheduler()->scheduleDelayedTask($task, 20 * 1); // 1200 tick = 1 dakika (20 tick/saniye).
        return true;
    }
}

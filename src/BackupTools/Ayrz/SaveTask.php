<?php

namespace BackupTools\Ayrz;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use ZipArchive;

class SaveTask extends Task
{
    private $backupName;

    public function __construct(string $backupName)
    {
        $this->backupName = $backupName;
    }

    public function onRun(): void
    {
        $date = date("Y-m-d_H-i-s");
        $backupName = $this->backupName . "/backup-" . $date . ".zip";
        $zip = new ZipArchive();
        if ($zip->open($backupName, ZipArchive::CREATE) === TRUE) {
            $zip->addEmptyDir("plugins");
            $zip->addEmptyDir("plugin_data");
            $zip->addEmptyDir("crashdumps");
            $zip->addEmptyDir("players");

            // $zip->addEmptyDir("worlds"); coming soon

            $this->zipDir(Main::getAPI()->getServer()->getDataPath() . "/plugins", $zip, "plugins");
            $this->zipDir(Main::getAPI()->getServer()->getDataPath() . "/plugin_data", $zip, "plugin_data");
            $this->zipDir(Main::getAPI()->getServer()->getDataPath() . "/players", $zip, "players");
            $this->zipDir(Main::getAPI()->getServer()->getDataPath() . "/crashdumps", $zip, "crashdumps");


            // $this->zipDir(Main::getAPI()->getServer()->getDataPath() . "/worlds", $zip, "worlds"); coming soon

            $serverLogPath = Main::getAPI()->getServer()->getDataPath() . "/server.log";
            if (file_exists($serverLogPath)) {
                $zip->addFile($serverLogPath, "server.log");
            } else {
                Main::getAPI()->getServer()->getLogger()->error("server.log dosyası bulunamadı.");
            }

            $zip->close();
            Main::getAPI()->getServer()->getLogger()->info("Sunucu başarıyla yedeklendi: " . $backupName);
        } else {
            Main::getAPI()->getServer()->getLogger()->error("Yedekleme oluşturulurken bir hata oluştu.");
        }
    }


    private function zipDir($path, $zip, $zipSubDir = "")
    {
        if (is_dir($path)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen(realpath($path)) + 1);
                    if ($zipSubDir !== "") {
                        $relativePath = $zipSubDir . "/" . $relativePath;
                    }
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }
}

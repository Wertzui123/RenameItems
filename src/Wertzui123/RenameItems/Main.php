<?php

namespace Wertzui123\RenameItems;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Wertzui123\RenameItems\commands\block;
use Wertzui123\RenameItems\commands\rename;

class Main extends PluginBase
{

    /** @var float */
    const CONFIG_VERSION = 2.0;

    /** @var Config */
    private $stringsFile;

    public function onEnable(): void
    {
        $this->ConfigUpdater();
        $this->stringsFile = new Config($this->getDataFolder() . "strings.yml", Config::YAML);
        $this->getServer()->getCommandMap()->register("RenameItems", new rename($this));
        $this->getServer()->getCommandMap()->register("RenameItems", new block($this));
    }

    /**
     * Returns a string from the strings file
     * @param string $key
     * @param array $replace [optional]
     * @return string
     */
    public function getString($key, $replace = []){
        return str_replace(array_keys($replace), $replace, $this->stringsFile->getNested($key));
    }

    /**
     * Returns a message from the strings file
     * @see Main::getString()
     * @param string $key
     * @param array $replace [optional]
     * @return string
     */
    public function getMessage($key, $replace = []){
        return $this->getString($key, $replace);
    }

    /**
     * @api
     * Checks whether the given item is blocked (can't be renamed)
     * @param Item $item
     * @return bool
     */
    public function isBlocked(Item $item){
        return in_array($item->getId(), $this->getConfig()->get('blocked_items')) || ($item->getNamedTag()->hasTag("RenameItems") && $item->getNamedTag()->getString("RenameItems") === "blocked");
    }

    /**
     * @api
     * Blocks the given item from being renamed
     * @param Item $item
     * @param bool $unblock [optional]
     */
    public function block(Item &$item, $unblock = false){
        $unblock ? $item->getNamedTag()->removeTag("RenameItems") : $item->getNamedTag()->setString("RenameItems", "blocked");
    }

    /**
     * @api
     * Opens an ui to rename the item in the players hand
     * @param bool $permissionCheck [optional]
     * @param Player $player
     */
    public function openRenameUI(Player $player, $permissionCheck = true){
        $form = new CustomForm(function (Player $player, $data) use ($permissionCheck){
            if(is_null($data)) return;
            if ($permissionCheck && !$player->hasPermission("renameitems.cmd.rename")) {
                $player->sendMessage($this->getMessage('command.rename.noPermission'));
                return;
            }
            $item = $player->getInventory()->getItemInHand();
            if ($item->isNull()) {
                $player->sendMessage($this->getMessage('command.rename.noItem'));
                return;
            }
            if ($this->isBlocked($item) && !$player->hasPermission("renameitems.blockeditems.bypass")) {
                $player->sendMessage($this->getMessage('command.rename.blocked'));
                return;
            }
            $item->setCustomName($data['name']);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage($this->getMessage('command.rename.success', ["{name}" => $data['name']]));
        });
        $form->setTitle($this->getString('ui.rename.title'));
        $form->addInput($this->getString('ui.rename.description'), $this->getString('ui.rename.replacement'), '', 'name');
        $player->sendForm($form);
    }

    /**
     * Checks whether the config version is the latest and updates it if it isn't
     */
    private function ConfigUpdater()
    {
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            $this->saveResource('config.yml');
            $this->saveResource('strings.yml');
            return;
        }
        if ($this->getConfig()->get('config-version') !== self::CONFIG_VERSION) {
            $config_version = $this->getConfig()->get('config-version');
            $this->getLogger()->info("§eYour Config isn't the latest. RenameItems renamed your old config to §bconfig-" . $config_version . ".yml §6and created a new config. Have fun!");
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config-" . $config_version . ".yml");
            rename($this->getDataFolder() . "strings.yml", $this->getDataFolder() . "strings-" . $config_version . ".yml");
            $this->saveResource("config.yml");
            $this->saveResource("messages.yml");
        }
    }

}
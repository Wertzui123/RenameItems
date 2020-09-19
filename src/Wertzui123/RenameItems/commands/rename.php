<?php

declare(strict_types=1);

namespace Wertzui123\RenameItems\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use Wertzui123\RenameItems\Main;
use pocketmine\Player;

class rename extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested("command.rename.command"), $plugin->getConfig()->getNested("command.rename.description"), $plugin->getConfig()->getNested("command.rename.usage"), $plugin->getConfig()->getNested("command.rename.aliases"));
        $this->setPermission("renameitems.cmd.rename");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.runIngame'));
            return;
        }
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.noPermission'));
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->isNull()) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.noItem'));
            return;
        }
        if ($this->plugin->isBlocked($item) && !$sender->hasPermission("renameitems.blockeditems.bypass")) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.blocked'));
            return;
        }
        if (!isset($args[0])) {
            $this->plugin->openRenameUI($sender);
            return;
        }
        $item->setCustomName(str_replace("\\n", "\n", implode(' ', $args)));
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage($this->plugin->getMessage('command.rename.success', ["{name}" => str_replace("\\n", "\n", implode(' ', $args))]));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

}

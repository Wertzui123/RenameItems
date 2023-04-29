<?php

declare(strict_types=1);

namespace Wertzui123\RenameItems\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use Wertzui123\RenameItems\Main;
use pocketmine\player\Player;

class RenameCommand extends Command implements PluginOwned
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.rename.command'), $plugin->getConfig()->getNested('command.rename.description'), $plugin->getConfig()->getNested('command.rename.usage'), $plugin->getConfig()->getNested('command.rename.aliases'));
        $this->setPermissions(['renameitems.command.rename']);
        $this->setPermissionMessage($plugin->getMessage('command.rename.noPermission'));
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.runIngame'));
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->isNull()) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.noItem'));
            return;
        }
        if ($this->plugin->isBlocked($item) && !$sender->hasPermission('renameitems.blockeditems.bypass')) {
            $sender->sendMessage($this->plugin->getMessage('command.rename.blocked'));
            return;
        }
        if (!isset($args[0])) {
            $this->plugin->openRenameUI($sender);
            return;
        }
        $item->setCustomName(str_replace('{line}', "\n", implode(' ', $args)));
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage($this->plugin->getMessage('command.rename.success', ['{name}' => str_replace('{line}', "\n", implode(' ', $args))]));
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

}
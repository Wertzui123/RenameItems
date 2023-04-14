<?php

declare(strict_types=1);

namespace Wertzui123\RenameItems\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use Wertzui123\RenameItems\Main;
use pocketmine\player\Player;

class BlockCommand extends Command implements PluginOwned
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.block.command'), $plugin->getConfig()->getNested('command.block.description'), $plugin->getConfig()->getNested('command.block.usage'), $plugin->getConfig()->getNested('command.block.aliases'));
        $this->setPermissions(['renameitems.command.block']);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.block.runIngame'));
            return;
        }
        if (!$sender->hasPermission($this->getPermissions()[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.block.noPermission'));
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->isNull()) {
            $sender->sendMessage($this->plugin->getMessage('command.block.noItem'));
            return;
        }
        $block = true;
        if (isset($args[0]) && $args[0] === 'false') {
            $block = false;
        }
        if ($block) {
            if ($this->plugin->isBlocked($item)) {
                $sender->sendMessage($this->plugin->getMessage('command.block.blocked'));
                return;
            }
        } else {
            if (!$this->plugin->isBlocked($item)) {
                $sender->sendMessage($this->plugin->getMessage('command.block.unblocked'));
                return;
            }
        }
        $this->plugin->block($item, !$block);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage($this->plugin->getMessage($block ? 'command.block.success' : 'command.block.unblock_success'));
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

}
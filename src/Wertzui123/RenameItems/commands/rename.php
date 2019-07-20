<?php

declare(strict_types=1);

namespace Wertzui123\RenameItems\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use Wertzui123\RenameItems\Main;
use pocketmine\Player;

class rename extends Command
{

    private $plugin;
    private $config;

    public function __construct(Main $plugin)
    {
        $config = $plugin->Config()->getAll();
        parent::__construct($config["command"] ?? "rename", $config["command_description"] ?? "Change item names", null, $config["command_aliases"] ?? []);
        $this->setPermission("renameitems.rename.cmd");
        $this->plugin = $plugin;
        $this->config = $config;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $msgs = $this->plugin->getMSGS()->getAll();
        if ($sender instanceof Player) {
            if ($sender->hasPermission($this->getPermission())) {
                $item = $sender->getInventory()->getItemInHand();
                if (!isset($args[0])) {
                    $sender->sendMessage($this->config["usage"]);
                    return;
                }
                if ($item->getId() == 0) {
                    $sender->sendMessage($msgs["hold_item"]);
                    return;
                }
                if(in_array($item->getId(), $this->config["banned_items"]) && !$sender->hasPermission("renameitems.blockeditems.bypass")){
                    $sender->sendMessage($msgs["item_banned"]);
                    return;
                }

                $item->setCustomName(implode(" ", $args));
                $sender->getInventory()->setItemInHand($item);
                $sender->sendMessage(str_replace("{name}", implode(" ", $args), $msgs["succes"]));
            }else{
                $sender->sendMessage($msgs["no_permission"]);
            }
        }else{
            $sender->sendMessage($msgs["run_ingame"]);
        }
    }
}

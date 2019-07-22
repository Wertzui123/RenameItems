<?php

namespace Wertzui123\RenameItems;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use Wertzui123\RenameItems\commands\rename;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{
	    $this->ConfigUpdater(1.1);
	    $this->getServer()->getCommandMap()->register("RenameItems", new rename($this));
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

public function Config(){
	    $cfg = new Config($this->getDataFolder()."config.yml", 2);
	    return $cfg;
}

public function getMSGS(){
	    $msgs = new Config($this->getDataFolder()."messages.yml", 2);
	    return $msgs;
}

public function ConfigUpdater($version){
	    $cfgpath = $this->getDataFolder()."config.yml";
	    $msgpath = $this->getDataFolder()."messages.yml";
	    if(file_exists($cfgpath)){
	        $cfgversion = $this->Config()->get("version");
	        if($cfgversion !== $version){
	            $this->getLogger()->info("Your config has been renamed to config-".$cfgversion.".yml and your messages file has been renamed to messages-".$cfgversion.".yml. That's because your config version wasn't the latest avable. So we created a new config and a new messages file for you!");
	            rename($cfgpath, $this->getDataFolder()."config-".$cfgversion.".yml");
                rename($msgpath, $this->getDataFolder()."messages-".$cfgversion.".yml");
                $this->saveResource("config.yml");
                $this->saveResource("messages.yml");
	        }
        }else{
            $this->saveResource("config.yml");
            $this->saveResource("messages.yml");
        }
}
}

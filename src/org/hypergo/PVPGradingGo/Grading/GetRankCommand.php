<?php
namespace org\hypergo\PVPGradingGo\Grading;

use pocketmine\event\Listener;
use pocketmine\command\
{
   Command,
   CommandSender,
   PluginCommand,
   CommandExecutor   
};

class GetRankCommand implements CommandExecutor{
   private $main;
   public function __construct($main){
      $this->main = $main;
      $this->registerCommand();
   }
   private function registerCommand(){
      $command = $this->main->getServer()->getCommandMap()->getCommand("getrank");
		    	if(($command !== null) && ($command instanceof PluginCommand) && ($command->getPlugin() === $this->main)){
      				$command->setExecutor($this);
		    	}
   }
   public function onCommand(\pocketmine\command\CommandSender $sender,\pocketmine\command\Command $command,string $label,array $args):bool{
      if($command->getName() == "getrank"){
         if(!isset($args[0])){
            $sender->sendMessage("[PVP段位系统] 用法: /getrank [玩家名]");
            return false;
         }
         $data = new \org\hypergo\PVPGradingGo\Player\Player($args[0]);
         $sender->sendMessage("[PVP段位系统] 玩家".$args[0]."的段位信息如下:\n".$data->getAllData());
         return true;
      }
   }
   /*
   public function onPlayerCommand(PlayerCommandPreprocessEvent $event){
      $player = $event->getPlayer();
      $message = $event->getMessage();
      if(substr($message,0,1) == "/"){
         $command = substr($message,1);
         $args = explode($command);
         if($args[0] == "getrank"){
            if(!isset($args[1])){
               $player->sendMessage("[PVP段位系统] 用法: /getrank [玩家名]");
               return;
            }
            $event->setCancelled();
            $data = new \org\hypergo\PVPGradingGo\Player\Player($args[1]);
            $player->sendMessage("[PVP段位系统] 玩家".$args[1]."的段位信息如下:\n".$data->getAllData());
         }
      }
   }
   */
}
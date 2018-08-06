<?php
namespace org\hypergo\PVPGradingGo\Prize;
date_default_timezone_set('prc');
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class Prize implements Listener{
   private $main;
   public function __construct($main){
      $this->main = $main;
   }
   public function onPlayerJoin(PlayerJoinEvent $event){
      $player = $event->getPlayer();
      $name = $player->getName();
      $data = new \org\hypergo\PVPGradingGo\Player\Player($name);
      $date = $data->getPrizeTime();
      if($date == "" || strtotime($date) < strtotime(date("y-m-d"))){
         $cmds = $this->main->getApi("grading")->getRankData($data->getRanking())["奖励"];
         foreach($cmds as $cmd){
            $this->main->getServer()->dispatchCommand(new \pocketmine\command\ConsoleCommandSender(),$cmd);
         }
         $player->sendMessage("你已获得[".$data->getRanking()."]段位的每日奖励");
      }
   }
}
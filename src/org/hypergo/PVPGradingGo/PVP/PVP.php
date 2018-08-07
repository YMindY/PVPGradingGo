<?php
namespace org\hypergo\PVPGradingGo\PVP;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\{PlayerGameModeChangeEvent};
use pocketmine\event\entity\
{
   EntityTeleportEvent,
   EntityDamageEvent,
   EntityDamagebyEntityEvent,
   EntityEffectAddEvent
};
use pocketmine\utils\Config;
class PVP implements Listener{
   private $main;
   private $conf;
   public function __construct($main){
      $this->main = $main;
   }
   private function getDataFolder(){
      return $this->main->getDataFolder()."PVPworld/";
   }
   private function registerConfig(){
      @mkdir($this->getDataFolder());
      $this->conf = new Config($this->getDataFolder()."Config.yml",Config::YAML,array("pvp世界"=>["pvp"]));
   }
   public function onPVP(EntityDamageEvent $event){
      if(!$event instanceof EntityDamagebyEntityEvent) return;
      $player = $event->getEntity();
      $damager = $event->getDamager();
      if(!$player instanceof Player || !$damager instanceof Player) return;
      if(in_array($event->getDamager()->getLevel()->getName(),$this->conf->get("pvp世界"))){
         if($event->getFinalDamage() >= $player->getHealth()){
            $up = $this->main->getApi("grading")->upPlayerGrade($damager->getName());
            $data = new \org\hypergo\PVPGradingGo\Player\Player($damager->getName());
            switch($up){
               case 1:
                  $damager->sendMessage("[PVP段位系统] 击杀成功!人头数(星数)加一\n你的段位星数: ".$data->getKills()." 总击杀数: ".$data->getTotalKills());
               break;
               case 2:
                  $damager->sendMessage("[PVP段位系统] 击杀成功! 段位等级(小段)提升!\n当前段位: ".$data->getRanking().$data->getLevel());
               break;
               case 3:
                  $damager->sendMessage("[PVP段位系统] 击杀成功! 段位晋级!\n当前段位: ".$data->getRanking());
               break;
               case 4:
                  $damager->sendMessage("[PVP段位系统] 击杀成功!段位已满，总人头数加一\n你的总击杀数: ".$data->getTotalKills());
               break;
            }
         }
      }else{
         $damager->sendMessage("[PVP段位系统] 不可以在指定地图以外pvp!");
         $event->setCancelled();
      }
      //to do
   }
   public function onWorldChange(EntityTeleportEvent $event){
      $player = $event->getEntity();
      if(!$player instanceof Player) return;
      if(in_array($event->getTo()->getLevel()->getName(),$this->conf->get("pvp世界"))){
         $player->removeAllEffects();
         $player->setGameMode(0);
      }
   }
   public function onEffect(EntityEffectAddEvent $event){
      $player = $event->getEntity();
      if(!$player instanceof Player) return;
      if(in_array($player->getLevel()->getName(),$this->conf->get("pvp世界"))){
         $event->setCancelled();
      }
   }
   public function onGameModeChange(PlayerGameModeChangeEvent $event){
      $player = $event->getPlayer();
      if(in_array($player->getLevel()->getName(),$this->conf->get("pvp世界"))){
         if($event->getNewGameMode() != 0){
            $event->setCancelled();
         }
      }
   }
}
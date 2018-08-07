<?php
namespace org\hypergo\PVPGradingGo\PVP;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\{PlayerGameModeChangeEvent};
use pocketmine\event\entity\
{
   EntityTelePortEvent,
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
      //to do
   }
   public function onWorldChange(EntityTelePortEvent $event){
      
   }
   public function onEffect(EntityEffectAddEvent $event){
      
   }
}
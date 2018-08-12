<?php
namespace org\hypergo\PVPGradingGo\PVP;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\
{
   PlayerGameModeChangeEvent,
   PlayerDeathEvent
};
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
      $this->registerConfig();
   }
   private function getDataFolder(){
      return $this->main->getDataFolder()."PVPworld/";
   }
   private function registerConfig(){
      @mkdir($this->getDataFolder());
      $this->conf = new Config($this->getDataFolder()."Config.yml",Config::YAML,array(
      "pvp世界"=>["pvp"],
      "击杀特效"=>[
         "2杀"=>"@killer完成了一次双杀",
         "3杀"=>"@killer正在大杀特杀",
         "4杀"=>"@killer杀人如麻",
         "5杀"=>"@killer已经主宰服务器!",
         "6杀"=>"@killer接近神了!!!",
         "7杀"=>"!!!!!@killer超越神了!!!!!"]
      ));
   }
   private function noticeTip($message){
      foreach($this->main->getServer()->getOnlinePlayers() as $player){
         $player->sendTip($message."\n\n\n\n\n\n\n\n\n\n");
      }
   }
   public function onPVP(EntityDamageEvent $event){
      if(!$event instanceof EntityDamagebyEntityEvent) return;
      $player = $event->getEntity();
      $damager = $event->getDamager();
      if(!$player instanceof Player || !$damager instanceof Player) return;
      if(in_array($event->getDamager()->getLevel()->getName(),$this->conf->get("pvp世界"))){
         /*
         if($event->getFinalDamage() >= $player->getHealth()){
            $up = $this->main->getApi("grading")->upPlayerGrade($damager->getName());
            $data = new \org\hypergo\PVPGradingGo\Player\Player($damager->getName());
            switch($up){
               case 2:
                  $damager->sendMessage("[PVP段位系统] 击杀成功! 段位等级(小段)提升!\n当前段位: ".$data->getRanking().$data->getLevel());
               break;
               case 3:
                  $damager->sendMessage("[PVP段位系统] 击杀成功! 段位晋级!\n当前段位: ".$data->getRanking());
               break;
               case 4:
                  $damager->sendMessage("[PVP段位系统] 击杀成功!段位已满，总人头数加一\n你的总击杀数: ".$data->getTotalKills());
               break;
               case 5:
                  $damager->sendMessage("[PVP段位系统] 击杀成功!你已进入晋级赛!\n 再击杀".($this->main->getApi("grading")->getRankData($data->getRanking())["晋级赛人头"])."个玩家即可晋级段位");
               break;
            }
         }
         */
      }else{
         $damager->sendMessage("[PVP段位系统] 不可以在指定地图以外pvp!");
         $event->setCancelled();
      }
   }
   public function onKill(PlayerDeathEvent $event){
       //此处参照Matt[SuperCopy]
       $cause = $event->getEntity()->getLastDamageCause();
       if(!$cause instanceof EntityDamageByEntityEvent || !($cause->getDamager() instanceof Player) || $cause->getDamager()->getGameMode() !== 0) return;
       $killer = $cause->getDamager();
       $player = $cause->getEntity();
       $kname = $killer->getName();
       $pname = $player->getName();
       $event->setDeathMessage($pname."被".$kname."打包成礼物送回了主城");
       $up = $this->main->getApi("grading")->upPlayerGrade($kname);
       $data = new \org\hypergo\PVPGradingGo\Player\Player($pname);
       $kills = $data->getMultiKills();
       $data->upMultiKills();
       if($kills <= 7){
          if($kills > 1){
             $this->noticeTip(str_replace("@killer",$kname,$this->conf->get("击杀特效")[$kills."杀"]));
          }
       }else{
          $this->noticeTip(str_replace("@killer",$kname,$this->conf->get("击杀特效")["7杀"]));
       }
       switch($up){
          case 2:
             $killer->sendMessage("[PVP段位系统] 击杀成功! 段位等级(小段)提升!\n当前段位: ".$data->getRanking().$data->getLevel());
          break;
          case 3:
             $killer->sendMessage("[PVP段位系统] 击杀成功! 段位晋级!\n当前段位: ".$data->getRanking());
          break;
          case 4:
             $killer->sendMessage("[PVP段位系统] 击杀成功!段位已满，总人头数加一\n你的总击杀数: ".$data->getTotalKills());
          break;
          case 5:
             $killer->sendMessage("[PVP段位系统] 击杀成功!你已进入晋级赛!\n 再击杀".($this->main->getApi("grading")->getRankData($data->getRanking())["晋级赛人头"])."个玩家即可晋级段位");
          break;
       }
       $ddata = new \org\hypergo\PVPGradingGo\Player\Player($pname);
       $ddata->initMultiKills();
       if($ddata->getMultiKills() > 3){
          $this->noticeTip($kname."终结了".$pname);
       }
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
<?php
namespace org\hypergo\PVPGradingGo\RankList;

use org\hypergo\PVPGradingGo\Main;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\command\
{
   Command,
   CommandSender,
   PluginCommand,
   CommandExecutor   
};
use pocketmine\event\entity\EntityTeleportEvent;

class RankList implements Listener,CommandExecutor{
   private $main;
   private static 
   $conf,
   $data,
   $list;
   
   public function __construct($main){
      $this->main = $main;
      $this->registerConfig();
      $this->registerCommand();
      self::initData();
      $this->registerLists();
   }
   private function getDataFolder(){
      return $this->main->getDataFolder()."RankList/";
   }
   private function registerConfig(){
      @mkdir($this->getDataFolder());
      self::$conf = new Config($this->getDataFolder()."Config.yml",Config::YAML,array(
      "排行榜"=>[
      ]
      ));
   }
   private function addList(string $pos){
      self::$data[] = $pos;
      self::updateConfig();
      $this->createList($pos);
   }
   private function registerLists(){
      self::$list = array();
      foreach(self::$data as $pos){
         $this->createList($pos);
      }
   }
   private function createList(string $pos){
      $data = explode(":",$pos);
      self::$list[$pos] = new Chart($this->main->getServer()->getLevelbyName($data[3]),$data[0],$data[1],$data[2]);
      $this->setListData($pos,"1");
   }
   private function generateList(string $pos){
      self::$list[$pos]->spawnToAll();
   }
   private function generateListToOne(string $pos,Player $player){
      self::$list[$pos]->spawnTo($player);
   }
   private function setListData(string $pos,string $data){
      self::$list[$pos]->setNameTag($data);
   }
   private function getListData(string $pos){
      return self::$list[$pos]->getNameTag($data);
   }
   private function getListWorld(string $pos){
      return $this->main->getServer()->getLevelbyName(explode(":",$pos)[3]);
   }
   private static function initData(){
      self::$data = self::$conf->get("排行榜");
   }
   private static function updateConfig(){
      self::$conf->set("排行榜",self::$data);
      self::$conf->save();
   }
   private function registerCommand(){
      $command = $this->main->getServer()->getCommandMap()->getCommand("ranklist");
		    	if(($command !== null) && ($command instanceof PluginCommand) && ($command->getPlugin() === $this->main)){
      				$command->setExecutor($this);
		    	}
   }
   public function onCommand(CommandSender $sender, Command $command, $label, array $args){
      if($command->getName() == "ranklist"){
         if(!isset($args[0])){
            $sender->sendMessage("[PVP段位系统] 用法: /ranklist here");
            return false;
         }
         if(!$sender instanceof Player){
            $sender->sendMessage("控制台一边去!");
            return false;
         }
         $pos = (int)$sender->getX().":".(int)$sender->getY().":".(int)$sender->getZ().":".$sender->getLevel()->getName();
         if($args[0] == "here"){
            $this->addList($pos);
            $this->setListData($pos,"1");
            $this->generateList($pos);
         }elseif($args[0] == "up"){
            foreach(self::$list as $pos=>$list){
               $this->setListData($pos,$this->getListData($pos)+1);
            }
         }
         return true;
      }
   }
   public function onTP(EntityTeleportEvent $event){
      if(!$event->getEntity() instanceof Player) return;
      foreach(self::$list as $pos=>$list){
         if($this->getListWorld($pos)->getName() == $event->getTo()->getLevel()->getName()){
            $this->generateListToOne($pos,$event->getEntity());
         }
      }
   }
}
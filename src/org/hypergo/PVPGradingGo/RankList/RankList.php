<?php
namespace org\hypergo\PVPGradingGo\RankList;

use org\hypergo\PVPGradingGo\Main;

use pocketmine\Player;
use pocketmine\entity\Entity;
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

use org\hypergo\PVPGradingGo\RankList\Chart;
use org\hypergo\PVPGradingGo\Grading\Grading;
use org\hypergo\PVPGradingGo\Player\Player as Data;
function longest(array $strs){
   $long = 0;
   foreach($strs as $str){
      $strlong = strlen($str);
      if($strlong > $long) $long = $strlong;
   }
   return $long;
}
function createBlack(int $num){
   $str = "";
   for($i=0;$i<$num;$i++){
      $str .= " ";
   }
   return $str;
}
class RankList implements Listener,CommandExecutor{
   private $main;
   private static 
   $conf,//配置文件
   $data,//排行榜坐标列表
   $list,//排行榜对象
   $player;//玩家排名
   
   public function __construct($main){
      $this->main = $main;
      $this->registerConfig();
      $this->registerPlayers();
      $this->registerCommand();
      self::initData();
      Entity::registerEntity(Chart::class,true);
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
      self::$list[$pos] = Chart::create($this->main->getServer()->getLevelbyName($data[3]),$data[0],$data[1],$data[2]);
      //$this->setListData($pos,"1");
   }
   private function generateList(string $pos){
      foreach($this->main->getServer()->getOnlinePlayers() as $player){
	        $this->generateListToOne($pos,$player);
      	}
   }
   private function generateListToOne(string $pos,Player $player){
      self::$list[$pos]->spawnTo($player);
   }
   private static function setListsData(string $data){
      foreach(self::$list as $pos=>$list){
         self::setListData($pos,$data);
      }
   }
   private static function setListData(string $pos,string $data){
      self::$list[$pos]->setNameTag($data);
   }
   private static function getListData(string $pos){
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
   //getPlayersFromConfig
   private function registerPlayers(){
      self::$player = [];
      $path = Grading::getDataFolder()."玩家信息/";
      $files = glob($path."*.yml");
      foreach($files as $file){
         if(!preg_match_all('/\/([^\/\n]*).yml/',$file,$name)) return;
         $name = $name[1][0];
         $data = new Data($name);
         self::$player[$name] = $data->getTotalKills();
      }
      self::arrangePlayers();
   }
   private static function arrangePlayers(){
      arsort(self::$player);
      $count = count(self::$player);
      if($count >= 6){
         self::$player = array_slice(self::$player,0,6,true);
      }
   }
   public static function upgradePlayer(string $name,$kills){
      self::$player[$name] = $kills;
      self::arrangePlayers();
      self::freshList();
   }
   private static function createListData():string{
      $longest = longest(array_keys(self::$player));
      $black = createBlack($longest-3);
      $str = "        ==PVP段位排行榜==\n排名 玩家".$black."段位等级    星数 杀人数";
      $poi = 1;
      foreach(self::$player as $name=>$kills){
         $data = new Data($name);
         $str .= 
         "\n ".
         $poi
         ."   ".
         $name
         .createBlack($longest-strlen($name)+1).
         $data->getRanking()
         ."--".
         str_replace(["1","2","3","4","5","6","7","8","9","0"],["一","二","三","四","五","六","七","八","九","零"],$data->getLevel())
         ."  ".
         $data->getKills()
         ."    ".
         $data->getTotalKills();
         $poi++;
      }
      if($poi < 6){
         for(;$poi <= 6;$poi++){
            $str .= 
            "\n "
            .$poi.
            "   ----"
            .$black.
            "------------  --  --";
         }
      }
      //var_dump($str);
      return (string)$str;
   }
   private static function freshList(){
      self::setListsData(self::createListData());
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
            $this->generateList($pos);
            self::freshList();
         }elseif($args[0] == "up"){
            $this->upgradePlayer("xMing",999);
         }
         return true;
      }
   }
   public function onTP(EntityTeleportEvent $event){
      if(!$event->getEntity() instanceof Player) return;
      foreach(self::$list as $pos=>$list){         
         if($this->getListWorld($pos)->getName() == $event->getTo()->getLevel()->getName()){
            $this->generateListToOne($pos,$event->getEntity());
            self::freshList();
         }
      }
   }
}
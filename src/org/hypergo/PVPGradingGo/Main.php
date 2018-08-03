<?php
namespace org\hypergo\PVPGradingGo;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use org\hypergo\PVPGradingGo\grading\Itf;
class Main extends PluginBase{
   private static $instance;
   public static function getInstance(){
      return self::$instance;
   }
   private $ApiList = [
      "grading"=>Itf::class
   ];
   private $apis = [];
   private function registerEvents(Listener $l){
      $this->getServer()->getPluginManager()->registerEvents($l,$this);
   }
   private function registerApis(){
      foreach($this->ApiList as $n => $a){
         $this->apis[$n] = new $a();
      }
   }
   protected function getApi($n){
      return $this->apis[$n];
   }
   public function onLoad(){
      $this->getLogger()->info("正在加载!");      
      @mkdir($this->getDataFolder(),0777,true);
      $this->registerApis();
   }
   public function onEnable(){
      self::$instance = $this;
      $this->getLogger()->notice("插件 已启动! \n§b开发人员: HypergoStdio(Creay,xMing)");
   }
   public function onDisable(){
      $this->getLogger()->warning("插件 已关闭!");
   }
}
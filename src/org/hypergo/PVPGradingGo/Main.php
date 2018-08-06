<?php
namespace org\hypergo\PVPGradingGo;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use org\hypergo\PVPGradingGo\grading\Grading;
class Main extends PluginBase{
   private $ApiList = [
      "grading"=>Grading::class
   ];
   private $apis = [];
   private function registerEvents(Listener $l){
      $this->getServer()->getPluginManager()->registerEvents($l,$this);
   }
   private function registerApis(){
      foreach($this->ApiList as $n => $a){
         $this->apis[$n] = new $a($this);
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
      $this->getLogger()->notice("插件 已启动! \n>>§a特约魔改人员: dhdj大魔王\n§b开发人员: HypergoStdio(xMing,Creay)");
   }
   public function onDisable(){
      $this->getLogger()->warning("插件 已关闭!");
   }
}
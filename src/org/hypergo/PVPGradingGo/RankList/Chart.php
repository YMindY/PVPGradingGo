<?php
namespace org\hypergo\PVPGradingGo\RankList;

use org\hypergo\PVPGradingGo\Main;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Item;
use pocketmine\level\Level;
use pocketmine\network\protocol\{AddEntityPacket,Info};
use pocketmine\network\mcpe\protocol\{AddEntityPacket as mAddEntityPacket,Info as mInfo};
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\CompoundTag;

class Chart extends Entity{
 const NETWORK_ID=Item::NETWORK_ID;
 public static function create(Level $level,$x,$y,$z){
	  $nbt=new CompoundTag("",[
			"Pos"=>new ListTag("Pos",[
				new DoubleTag("",$x),
				new DoubleTag("",$y),
				new DoubleTag("",$z)
				]
			),
			"Motion"=>new ListTag("Motion",[
				new DoubleTag("",0),
				new DoubleTag("",0),
				new DoubleTag("",0)
				]
			),
			"Rotation"=>new ListTag("Rotation",[
				new FloatTag("",0),
				new FloatTag("",0)
				]
			)
		]);
		return new Chart($level,$nbt);
 }
	
	public function spawnTo(Player $player){
	 $pk=new AddEntityPacket();
		$pk->eid=$this->getId();
		$pk->type=Item::NETWORK_ID;
		$pk->x=$this->x;
		$pk->y=$this->y;
		$pk->z=$this->z;
		$pk->yaw=$this->yaw;
		$pk->pitch=$this->pitch;
		$pk->metadata=$this->dataProperties;
		$player->dataPacket($pk);
		//$this->setDataFlag(Entity::DATA_FLAGS,Entity::DATA_FLAG_INVISIBLE,true);
		$this->setImmobile(true);
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
		parent::spawnTo($player);
	}
}
<?php

namespace xenialdan\IsPregnant;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\level\LevelException;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;

class Loader extends PluginBase
{
	/** @var self */
	private static $instance;
	/**
	 * @var Skin
	 */
	private static $skin;

	/**
	 * @return self
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * @throws PluginException
	 */
	public function onLoad()
	{
		if (!extension_loaded("gd")) {
			throw new PluginException("GD library is not enabled! Please uncomment gd2 in php.ini!");
		}
		self::$instance = $this;
		$this->saveResource("belly.png");
		$this->saveResource("belly.json");
		self::$skin = new Skin("belly", self::fromImage(imagecreatefrompng($this->getDataFolder() . "belly.png")), "", "geometry.belly", file_get_contents($this->getDataFolder() . "belly.json"));
		Entity::registerEntity(PregnantEntity::class, true, ['belly']);
	}

	/**
	 * from skinapi
	 * @param resource $img
	 * @return string
	 * @throws PluginException
	 */
	public static function fromImage($img)
	{
		$bytes = '';
		for ($y = 0; $y < imagesy($img); $y++) {
			for ($x = 0; $x < imagesx($img); $x++) {
				$rgba = @imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		@imagedestroy($img);
		return $bytes;
	}

	/**
	 * @param Level|null $level If given, searches only in that level
	 * @return PregnantEntity[]
	 */
	private static function getAllBellies(?Level $level = null): array
	{
		$entities = [];
		if ($level instanceof Level)
			$levels = [$level];
		else $levels = self::getInstance()->getServer()->getLevels();
		foreach ($levels as $level) {
			$entities = array_merge($entities, array_filter($level->getEntities(), function (Entity $entity) {
				return $entity instanceof PregnantEntity && $entity->isValid() && !$entity->isFlaggedForDespawn() && !$entity->isClosed();
			}));
		};
		return $entities;
	}

	/**
	 * @param Player $player
	 * @return PregnantEntity[]
	 */
	public static function getBellies(Player $player)
	{
		$id = $player->getId();
		return array_filter(self::getAllBellies($player->getLevel()), function (?PregnantEntity $wingEntity) use ($id) {
			return $wingEntity instanceof PregnantEntity && $wingEntity->getOwningEntityId() === $id;
		});
	}

	public function onDisable()
	{
		foreach (self::getAllBellies() as $wing) $wing->flagForDespawn();
	}

	public function onEnable()
	{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
		foreach ($this->getServer()->getOnlinePlayers() as $player) {
			if ($player->getGenericFlag(Entity::DATA_FLAG_PREGNANT))
				self::addPlayer($player);
		}
	}

	public static function removePlayer(Player $player): void
	{
		foreach (self::getBellies($player) as $wing) {
			$wing->flagForDespawn();
		}
	}

	/**
	 * @param Player $player
	 * @throws LevelException
	 */
	public static function addPlayer(Player $player): void
	{
		self::removePlayer($player);
		if (self::$skin instanceof Skin) {
			$player->setGenericFlag(Entity::DATA_RIDER_ROTATION_LOCKED, true);
			$nbt = Entity::createBaseNBT($player, null, $player->getYaw());
			$we = new PregnantEntity(self::$skin, $player->getLevel(), $nbt);
			$we->getDataPropertyManager()->setLong(Human::DATA_OWNER_EID, $player->getId());
			$player->getLevel()->addEntity($we);
			$we->spawnToAll();
			$pk = new SetActorLinkPacket();
			$pk->link = new EntityLink($player->getId(), $we->getId(), EntityLink::TYPE_PASSENGER, true);
			$player->getLevel()->broadcastGlobalPacket($pk);
		}
	}
}
<?php

namespace xenialdan\IsPregnant;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class EventListener implements Listener
{

	public function onJoin(PlayerJoinEvent $event): void
	{
		if (strpos($event->getPlayer()->getSkin()->getSkinId(), "Alex") !== false) {
			$event->getPlayer()->setGenericFlag(Entity::DATA_FLAG_PREGNANT);
		}
		if ($event->getPlayer()->getGenericFlag(Entity::DATA_FLAG_PREGNANT))
			Loader::addPlayer($event->getPlayer());
	}

	public function onQuit(PlayerQuitEvent $event): void
	{
		Loader::removePlayer($event->getPlayer());
	}

	public function onRespawn(PlayerRespawnEvent $event): void
	{
		if ($event->getPlayer()->getGenericFlag(Entity::DATA_FLAG_PREGNANT)) {
			Loader::removePlayer($event->getPlayer());
			Loader::addPlayer($event->getPlayer());
			return;
		}
	}
}
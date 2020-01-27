<?php

namespace xenialdan\IsPregnant;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerChangeSkinEvent;
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
        if (Loader::isPregnant($event->getPlayer()))
            Loader::addPlayer($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        Loader::removePlayer($event->getPlayer());
    }

    public function onRespawn(PlayerRespawnEvent $event): void
    {
        if (Loader::isPregnant($event->getPlayer())) {
            Loader::removePlayer($event->getPlayer());
            Loader::addPlayer($event->getPlayer());
        }
    }

    public function onTeleport(EntityTeleportEvent $event): void
    {
        if (Loader::isPregnant($event->getEntity())) {
            Loader::removePlayer($event->getEntity());
            Loader::addPlayer($event->getEntity());
        }
    }

    public function onSkinChange(PlayerChangeSkinEvent $event): void
    {
        if ($event->isCancelled()) return;
        Loader::removePlayer($event->getPlayer());
        $event->getPlayer()->setGenericFlag(Entity::DATA_FLAG_PREGNANT, (strpos($event->getNewSkin()->getSkinId(), "Alex") !== false));
        if (Loader::isPregnant($event->getPlayer()))
            Loader::addPlayer($event->getPlayer());
    }

    /* This adds just maybe 1 tick faster response to change sneaking state of belly
     * public function onSneak(PlayerToggleSneakEvent $event): void
    {
        foreach (Loader::getBellies($event->getPlayer()) as $backpack) {
            $backpack->updateScale();
        }
    }*/

    public function onBedLeave(PlayerBedLeaveEvent $event): void
    {
        $player = $event->getPlayer();
        Loader::removePlayer($player);
        if (Loader::isPregnant($player)) {
            Loader::addPlayer($player);
        }
    }

    public function onBedEnter(PlayerBedEnterEvent $event): void
    {
        Loader::removePlayer($event->getPlayer());
    }
}
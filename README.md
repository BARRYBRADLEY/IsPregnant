![Pregnant Alex](https://github.com/thebigsmileXD/IsPregnant/blob/master/resources/pregnantalex2.jpg)
---
# IsPregnant
Yes no, i am fine.
Sorry if this is weird or disturbing to you

Everyone who joins with an "Alex" skin, or switches to one of those, will become "pregnant"..
## Why?!
![Mojang isPregnant in Player data](https://github.com/thebigsmileXD/IsPregnant/blob/master/resources/ThisIsWhy.png)

Mojang saves every possible property in any entity, even player. So it happens that player save data contains the `IsPregnant` data flag. I decided to make some kind of a meme out of that.

Actually, the mechanic behind that system is quite interesting. If you'd like, check out https://github.com/thebigsmileXD/Backpack where i actually made practical use out of the "wearable geometry" concept created in this plugin.
## Information about the messages
When a player is set as pregnant, the addPlayer function sends a message to the player.
The sent message contains one random word from this video: https://www.youtube.com/watch?v=EShUeudtaFg (Thanks to [@Sandertv](https://github.com/Sandertv/) for the idea and that guy who made an alphabetically ordered list in the video's comment section!)
## API
You can set the data flag to turn a player pregnant (or not pregnant anymore) like this:
```php
/** @var Player $player */
$player->setGenericFlag(Entity::DATA_FLAG_PREGNANT);
if (Loader::isPregnant($event->getPlayer()))
    Loader::addPlayer($event->getPlayer());
```
Do not forget to import Loader.

When you set the data flag to false, the entity will automatically be removed
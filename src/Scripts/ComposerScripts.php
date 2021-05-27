<?php
namespace MPOS\Scripts;

use Composer\Script\Event;

class ComposerScripts
{
    public static function devModeOnly(Event $event)
    {
        if (!$event->isDevMode()) {
            $event->stopPropagation();
            print("Skipping {$event->getName()} as this is a non-dev installation.\n");
        }
    }
}

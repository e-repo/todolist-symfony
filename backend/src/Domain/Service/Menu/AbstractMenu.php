<?php

declare(strict_types=1);

namespace App\Domain\Service\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;

abstract class AbstractMenu
{
    abstract public function build(): ItemInterface;

    public function toArray(ItemInterface $menu): array
    {
        $sidebarMenuList = [];
        $subMenu = [];

        /** @var MenuItem $menuItem */
        foreach ($menu as $menuItem) {
            if (true === $menuItem->hasChildren()) {
                $subMenu = $this->toArray($menuItem);
            }

            $sidebarMenuList[] = [
                'name' => $menuItem->getName(),
                'uri' => $menuItem->getUri(),
                'subMenu' => $subMenu,
            ];
        }

        return $sidebarMenuList;
    }
}
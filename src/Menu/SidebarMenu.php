<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarMenu
{
    private FactoryInterface $factory;
    private TranslatorInterface $translator;

    /**
     * SidebarMenu constructor.
     * @param FactoryInterface $factory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator
    )
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }

    public function buildSidebarMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'c-sidebar-nav');

        $menu
            ->addChild('Home', ['route' => 'home'])
            ->setAttribute('class', 'c-sidebar-nav-item')
            ->setLinkAttribute('class', 'c-sidebar-nav-link')
            ->setExtra('icon', 'c-sidebar-nav-icon cil-home');

        $menu
            ->addChild(
                $this->translator->trans('Users', [], 'profile'),
                ['route' => 'users']
            )
            ->setAttribute('class', 'c-sidebar-nav-item')
            ->setLinkAttribute('class', 'c-sidebar-nav-link')
            ->setExtra('routes', [
                ['pattern' => '/^users\..+/']
            ])
            ->setExtra('icon', 'c-sidebar-nav-icon cil-people');

        $menu
            ->addChild('ToDo', ['route' => 'home'])
            ->setAttribute('class', 'c-sidebar-nav-item c-sidebar-nav-dropdown')
            ->setLinkAttribute('class', 'c-sidebar-nav-link c-sidebar-nav-dropdown-toggle')
            ->setExtra('icon', 'c-sidebar-nav-icon cil-list-rich')

                ->setChildrenAttribute('class', 'c-sidebar-nav-dropdown-items')

                ->addChild('New', ['route' => 'tasks.bar.published'])
                ->setAttribute('class', 'c-sidebar-nav-item')
                ->setLinkAttribute('class', 'c-sidebar-nav-link')
                ->setExtra('routes', [
                    ['pattern' => '/^tasks\.bar\.published.+/']
                ])

                ->getParent()

                ->addChild('Fulfilled', ['route' => 'tasks.bar.fulfilled'])
                ->setAttribute('class', 'c-sidebar-nav-item')
                ->setLinkAttribute('class', 'c-sidebar-nav-link')
                ->setExtra('routes', [
                    ['pattern' => '/^tasks\.bar\.fulfilled.+/']
                ]);

        return $menu;
    }
}
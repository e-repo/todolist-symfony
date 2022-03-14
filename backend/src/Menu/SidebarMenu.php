<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarMenu
{
    private FactoryInterface $factory;
    private TranslatorInterface $translator;
    private AuthorizationCheckerInterface $authorizationChecker;

    private ?ItemInterface $menu = null;

    /**
     * SidebarMenu constructor.
     * @param FactoryInterface $factory
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildSidebarMenu(): ItemInterface
    {
        if (null !== $this->menu) {
            return $this->menu;
        }

        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'c-sidebar-nav');

        $menu
            ->addChild('Home', ['route' => 'home'])
            ->setAttribute('class', 'c-sidebar-nav-item')
            ->setLinkAttribute('class', 'c-sidebar-nav-link')
            ->setExtra('icon', 'c-sidebar-nav-icon cil-home');

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
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
        }

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

        $this->menu = $menu;

        return $this->menu;
    }

    /**
     * @param ItemInterface $menu
     * @return array
     * @throws \Exception
     */
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
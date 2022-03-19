<?php

declare(strict_types=1);

namespace App\Menu\Api;

use App\Menu\AbstractMenu;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarMenuApi extends AbstractMenu
{
    private FactoryInterface $factory;
    private TranslatorInterface $translator;

    private ?ItemInterface $menuSidebar = null;

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

    public function build(): ItemInterface
    {
        if (null !== $this->menuSidebar) {
            return $this->menuSidebar;
        }

        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'c-sidebar-nav');

        $menu
            ->addChild('Home', ['route' => 'home'])
            ->getParent()

            ->addChild(
                $this->translator->trans('Users', [], 'profile'),
                ['uri' => '/api/v1/users']
            )
            ->getParent()

            ->addChild('ToDo', ['route' => 'home'])
            ->addChild('New', ['uri' => '/api/v1/tasks-published/list'])
            ->getParent()

            ->addChild('Fulfilled', ['uri' => '/api/v1/tasks-fulfilled/list']);

        $this->menuSidebar = $menu;

        return $this->menuSidebar;
    }
}
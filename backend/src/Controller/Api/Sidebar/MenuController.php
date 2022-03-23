<?php

declare(strict_types=1);

namespace App\Controller\Api\Sidebar;

use App\Menu\SidebarMenu;
use App\Service\JsonApi\JsonApiHelper;
use App\Service\JsonApi\ResponseBuilder\ResponseDataBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api", name="sidebar_menu")
 */
class MenuController extends AbstractController
{
    private JsonApiHelper $apiHelper;
    private SidebarMenu $sidebarMenu;
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param JsonApiHelper $apiHelper
     * @param SidebarMenu $sidebarMenu
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        JsonApiHelper         $apiHelper,
        SidebarMenu           $sidebarMenu,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->apiHelper = $apiHelper;
        $this->sidebarMenu = $sidebarMenu;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/v1/sidebar-menu", name=".list", methods={"GET"})
     * @return JsonResponse
     * @throws \Exception
     */
    public function getSidebarMenu(): JsonResponse
    {
        try {
            $rootSidebarMenuItem = $this->sidebarMenu->build();
            $sidebarMenuTree = $this->sidebarMenu->toArray($rootSidebarMenuItem);

            $linkSelf = $this->urlGenerator->generate(
                'sidebar_menu.list',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $responseDataBuilder = ResponseDataBuilder::create()
                ->setDataType('Sidebar menu')
                ->setLinkSelf($linkSelf)
                ->setDataAttribute('tree', $sidebarMenuTree);

            return $this->apiHelper->createJsonResponse($responseDataBuilder);
        } catch (\Throwable $e) {
            return $this->apiHelper->createJsonResponseFromError($e);
        }

    }
}
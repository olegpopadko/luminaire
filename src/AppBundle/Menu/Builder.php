<?php
/**
 * Created by Oleg Popadko
 * Date: 9/17/15
 * Time: 12:16 PM
 */

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem(
            'Luminaire',
            [
                'route'  => 'homepage',
                'extras' => ['menu_type' => 'topbar']
            ]
        );

        $this->buildAdminItem($menu);

        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild($this->container->get('translator')->trans('Login'), ['route' => 'login_route']);
        }

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->buildAuthenticatedFullyItems($menu);
        }

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private function buildAuthenticatedFullyItems(ItemInterface $menu)
    {
        $menu->addChild($this->container->get('translator')->trans('Project list', [], 'project'), [
            'route' => 'project',
        ]);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $menu->addChild($this->container->get('translator')->trans('Profile', [], 'profile'), [
            'route'           => 'profile',
            'routeParameters' => [
                'id' => $user->getId(),
            ]
        ]);

        $menu->addChild($this->container->get('translator')->trans('Logout'), ['route' => 'logout']);
    }

    /**
     * @param ItemInterface $menu
     */
    private function buildAdminItem(ItemInterface $menu)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu->addChild($this->container->get('translator')->trans('Administration'), ['uri' => '#'])
                ->addChild($this->container->get('translator')->trans('Users'), ['route' => 'admin_user']);
        }
    }
}

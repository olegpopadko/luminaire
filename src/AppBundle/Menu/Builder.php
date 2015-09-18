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

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild($this->container->get('translator')->trans('Logout'), ['route' => 'logout']);
        }

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private function buildAdminItem(ItemInterface $menu)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu->addChild($this->container->get('translator')->trans('Adminstration'))
                ->addChild($this->container->get('translator')->trans('Users'), ['route' => 'admin_user']);
        }
    }
}

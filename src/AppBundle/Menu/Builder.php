<?php
/**
 * Created by Oleg Popadko
 * Date: 9/17/15
 * Time: 12:16 PM
 */

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
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

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild('Logout', ['route' => 'logout']);
        }

        return $menu;
    }
}

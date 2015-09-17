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
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem(
            'Luminaire', [
                'route'  => 'homepage',
                'extras' => ['menu_type' => 'topbar']
            ]
        );

        return $menu;
    }
}

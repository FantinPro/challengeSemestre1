<?php

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as Loader;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractFixtureMigration extends \Doctrine\Migrations\AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function loadFixtures(array $fixtures, $append = true)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $loader = new Loader($this->container);
        array_map(array($loader, 'addFixture'), $fixtures);

        $purger = null;
        if ($append === false) {
            $purger = new ORMPurger($em);
            $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        }

        $executor = new ORMExecutor($em, $purger);

        $output = new ConsoleOutput;
        $executor->setLogger(function($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });

        $executor->execute($loader->getFixtures(), $append);
    }
}

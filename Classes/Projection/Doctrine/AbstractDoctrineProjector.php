<?php
namespace Neos\Cqrs\Projection\Doctrine;

/*
 * This file is part of the Neos.Cqrs package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Exception;
use Neos\Cqrs\Projection\AbstractBaseProjector;
use TYPO3\Flow\Annotations as Flow;

/**
 * A base class for Doctrine-based projectors
 *
 * @api
 */
abstract class AbstractDoctrineProjector extends AbstractBaseProjector
{
    /**
     * @Flow\Inject
     * @var DoctrineProjectionPersistenceManager
     */
    protected $projectionPersistenceManager;

    /**
     * Initialize the Read Model class name
     * Make sure to call this method as parent when overriding it in a concrete projector.
     *
     * @return void
     * @throws Exception
     */
    protected function initializeObject()
    {
        if ($this->readModelClassName === null) {
            if (substr(get_class($this), -9, 9) !== 'Projector') {
                throw new Exception(sprintf('The class name "%s" doesn\'t end in "Projector" so the Read Model class name can\'t be determined automatically. Please set the "readModelClassName" field it in your concrete projector.', get_class($this)), 1476799474);
            }
            $this->readModelClassName = substr(get_class($this), 0, -9);
        }
    }

    /**
     * Retrieves an object with the given $identifier.
     * For use in the concrete projector.
     *
     * @param string $identifier
     * @return object an instance of $this->readModelClassName or NULL if no matching object could be found
     * @api
     */
    public function get(string $identifier)
    {
        return $this->projectionPersistenceManager->get($this->getReadModelClassName(), $identifier);
    }

    /**
     * Adds an object to this repository.
     * For use in the concrete projector.
     *
     * @param object $object The object to add
     * @return void
     * @api
     */
    protected function add($object)
    {
        $this->projectionPersistenceManager->add($object);
    }

    /**
     * Schedules a modified object for persistence.
     * For use in the concrete projector.
     *
     * @param object $object The modified object
     * @return void
     * @throws \TYPO3\Flow\Persistence\Exception\IllegalObjectTypeException
     * @api
     */
    protected function update($object)
    {
        $this->projectionPersistenceManager->update($object);
    }

    /**
     * Removes an object from the projector's persistence.
     * For use in the concrete projector.
     *
     * @param object $object The object to remove
     * @return void
     * @api
     */
    protected function remove($object)
    {
        $this->projectionPersistenceManager->remove($object);
    }

    /**
     * Removes all objects of this repository as if remove() was called for all of them.
     * For usage in the concrete projector.
     *
     * @return void
     * @api
     */
    protected function drop()
    {
        $this->projectionPersistenceManager->drop($this->getReadModelClassName());
    }
}
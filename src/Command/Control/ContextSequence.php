<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM\Command\Control;

use Spiral\ORM\Command\ContextualCommandInterface;
use Spiral\ORM\Exception\CommandException;

/**
 * Wraps the sequence with commands and provides an ability to mock access to the primary command.
 */
class ContextSequence extends Sequence implements ContextualCommandInterface
{
    /** @var ContextualCommandInterface */
    private $primary;

    /**
     * Add primary command to the sequence.
     *
     * @param ContextualCommandInterface $command
     */
    public function addPrimary(ContextualCommandInterface $command)
    {
        $this->addCommand($command);
        $this->primary = $command;
    }

    /**
     * @return ContextualCommandInterface
     */
    public function getPrimary(): ContextualCommandInterface
    {
        if (empty($this->primary)) {
            throw new CommandException("Primary sequence command is not set");
        }

        return $this->primary;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(): array
    {
        return $this->getPrimary()->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(string $name, $value)
    {
        $this->getPrimary()->setContext($name, $value);
    }

    /**
     * Closure to be called after command executing.
     *
     * @param callable $closure
     */
    public function onExecute(callable $closure)
    {
        $this->getPrimary()->onExecute($closure);
    }

    /**
     * To be called after parent transaction been commited.
     *
     * @param callable $closure
     */
    public function onComplete(callable $closure)
    {
        $this->getPrimary()->onComplete($closure);
    }

    /**
     * To be called after parent transaction been rolled back.
     *
     * @param callable $closure
     */
    public function onRollBack(callable $closure)
    {
        $this->getPrimary()->onRollBack($closure);
    }
}
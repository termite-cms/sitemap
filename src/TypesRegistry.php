<?php declare(strict_types=1);

namespace Termite\Sitemap;

use Psr\EventDispatcher\EventDispatcherInterface;
use Termite\Sitemap\Event\Type as EventType;

final class TypesRegistry
{
    /** @var Type[] */
    private $types;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getTypes(): iterable
    {
        if ($this->types === null) {
            $this->fetchTypes();
        }

        yield from $this->types;
    }

    public function getType(string $type): Type
    {
        if ($this->types === null) {
            $this->fetchTypes();
        }

        return $this->types[$type];
    }

    private function fetchTypes(): void
    {
        $event = new EventType();
        $this->eventDispatcher->dispatch($event);
        $this->types = $event->getTypes();
    }
}

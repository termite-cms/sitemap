<?php declare(strict_types=1);

namespace Termite\Sitemap;

final class Type
{
    /** @var string */
    private $name;

    /** @var HandlerInterface */
    private $handler;

    public function __construct(string $name, HandlerInterface $handler)
    {
        $this->name = $name;
        $this->handler = $handler;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandler(): HandlerInterface
    {
        return $this->handler;
    }
}

<?php declare(strict_types=1);

namespace Termite\Sitemap\Event;

use ReactiveApps\Command\HttpServer\Thruway\Realm;
use Termite\Sitemap\Type as SitemapType;
use Thruway\ClientSession;

final class Type
{
    /** @var SitemapType[] */
    private $types = [];

    public function addType(SitemapType $type): void
    {
        $this->types[] = $type;
    }

    /**
     * @return SitemapType[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}

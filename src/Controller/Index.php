<?php declare(strict_types=1);

namespace Termite\Sitemap\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use ReactiveApps\Command\HttpServer\Annotations\Method;
use ReactiveApps\Command\HttpServer\Annotations\Routes;
use Termite\Sitemap\Event\Type;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Sitemap;
use Thepixeldeveloper\Sitemap\SitemapIndex;

final class Index
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Method("GET")
     * @Routes("/sitemap.xml")
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $event = new Type();
        $this->eventDispatcher->dispatch($event);

        $urlSet = new SitemapIndex();
        foreach ($event->getTypes() as $type) {
            $urlSet->add(new Sitemap('/sitemap/' . $type->getName() . '.xml'));
        }

        $driver = new XmlWriterDriver();
        $urlSet->accept($driver);

        return new Response(
            200,
            [
                'Content-Type:' => 'text/xml',
            ],
            $driver->output()
        );
    }
}
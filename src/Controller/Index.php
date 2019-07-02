<?php declare(strict_types=1);

namespace Termite\Sitemap\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use ReactiveApps\Command\HttpServer\Annotations\Method;
use ReactiveApps\Command\HttpServer\Annotations\Routes;
use Termite\Sitemap\TypesRegistry;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Sitemap;
use Thepixeldeveloper\Sitemap\SitemapIndex;

final class Index
{
    /** @var TypesRegistry */
    private $typeRegistry;

    public function __construct(TypesRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @Method("GET")
     * @Routes("/sitemap.xml")
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $urlSet = new SitemapIndex();
        foreach ($this->typeRegistry->getTypes() as $type) {
            $urlSet->add(new Sitemap('/sitemap/' . $type->getName() . '.xml'));
        }

        $driver = new XmlWriterDriver();
        $urlSet->accept($driver);

        return new Response(
            200,
            [
                'Content-Type:' => 'application/xml; charset=UTF-8',
            ],
            $driver->output()
        );
    }
}
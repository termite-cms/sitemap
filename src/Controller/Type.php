<?php declare(strict_types=1);

namespace Termite\Sitemap\Controller;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use ReactiveApps\Command\HttpServer\Annotations\Method;
use ReactiveApps\Command\HttpServer\Annotations\Routes;
use Termite\Sitemap\TypesRegistry;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Sitemap;
use Thepixeldeveloper\Sitemap\SitemapIndex;
use WyriHaximus\Annotations\Coroutine;

/**
 * @Coroutine()
 */
final class Type
{
    /** @var TypesRegistry */
    private $typeRegistry;

    public function __construct(TypesRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @Method("GET")
     * @Routes("/sitemap/{type:[a-zA-Z0-9\-]{1,}}.xml")
     */
    public function type(ServerRequestInterface $request)
    {
        $type = $this->typeRegistry->getType($request->getAttribute('type'));

        $count = yield $type->getHandler()->count();

        $urlSet = new SitemapIndex();
        for ($i = 0; $i < ceil($count / 1000); $i++) {
            $urlSet->add(new Sitemap('/sitemap/' . $type->getName() . '/' . $i . '.xml'));
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
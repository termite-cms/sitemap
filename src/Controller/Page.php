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
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;
use WyriHaximus\Annotations\Coroutine;

/**
 * @Coroutine()
 */
final class Page
{
    /** @var TypesRegistry */
    private $typeRegistry;

    public function __construct(TypesRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @Method("GET")
     * @Routes("/sitemap/{type:[a-zA-Z0-9\-]{1,}}/{page:[0-9]{1,}}.xml")
     */
    public function page(ServerRequestInterface $request)
    {
        $type = $this->typeRegistry->getType($request->getAttribute('type'));

        /** @var Url[] $urls */
        $urls = yield $type->getHandler()->page((int) $request->getAttribute('page'))->filter(function ($v) {
            return $v instanceof Url;
        })->toArray()->toPromise();

        $urlSet = new Urlset();
        foreach ($urls as $url) {
            $urlSet->add($url);
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
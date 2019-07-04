<?php declare(strict_types=1);

namespace Termite\Sitemap\Controller;

use DI\Annotation\Inject;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use ReactiveApps\Command\HttpServer\Annotations\Method;
use ReactiveApps\Command\HttpServer\Annotations\Routes;
use Termite\Sitemap\TypesRegistry;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Sitemap;
use Thepixeldeveloper\Sitemap\SitemapIndex;
use WoWScreenshotsNet\Model\ScreenshotsTableInterface;
use WyriHaximus\Annotations\Coroutine;

/**
 * @Coroutine()
 */
final class Type
{
    /** @var TypesRegistry */
    private $typeRegistry;

    /** @var string */
    private $fullBaseUrl;

    /**
     * @Inject({"fullBaseUrl" = "config.app.full_base_url"})
     */
    public function __construct(TypesRegistry $typeRegistry, string $fullBaseUrl)
    {
        $this->typeRegistry = $typeRegistry;
        $this->fullBaseUrl = $fullBaseUrl;
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
            $urlSet->add(new Sitemap($this->fullBaseUrl . '/sitemap/' . $type->getName() . '/' . $i . '.xml'));
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
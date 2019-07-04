<?php declare(strict_types=1);

namespace Termite\Sitemap\Controller;

use DI\Annotation\Inject;
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
     * @Routes("/sitemap.xml")
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $urlSet = new SitemapIndex();
        foreach ($this->typeRegistry->getTypes() as $type) {
            $urlSet->add(new Sitemap($this->fullBaseUrl . '/sitemap/' . $type->getName() . '.xml'));
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
<?php declare(strict_types=1);

namespace Termite\Sitemap;

use React\Promise\PromiseInterface;
use Rx\Observable;

interface HandlerInterface
{
    public function count(): PromiseInterface;

    public function page(int $page): Observable;
}

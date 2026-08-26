<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // The storefront's client bundle reads its catalogue, zones, testimonials
        // and gallery from `$dd`. Sharing it with the renderer here means every
        // shop page gets it without each controller having to remember.
        //
        // Admin pages never render that bundle, so they skip the queries.
        if (!str_starts_with(ltrim($request->getUri()->getPath(), '/'), 'admin')
            && !$this->isAdminPath($request)) {
            service('renderer')->setVar('dd', (new \App\Libraries\StorefrontData())->payload());
        }
    }

    /** True when the current request targets the dashboard, base-path aware. */
    private function isAdminPath(RequestInterface $request): bool
    {
        $path = '/' . ltrim($request->getUri()->getPath(), '/');
        $base = rtrim(parse_url(base_url(), PHP_URL_PATH) ?? '/', '/');

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return str_starts_with(ltrim($path, '/'), 'admin');
    }
}

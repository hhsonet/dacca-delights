<?php

namespace App\Controllers;

use App\Libraries\OrderPlacer;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Order extends BaseController
{
    /** POST /order — place a real order. */
    public function place(): ResponseInterface
    {
        // Light abuse guard: 12 order attempts per minute per IP.
        $throttler = Services::throttler();
        if ($throttler->check(md5('order-' . $this->request->getIPAddress()), 12, MINUTE) === false) {
            return $this->response->setStatusCode(429)->setJSON([
                'ok'     => false,
                'errors' => ['Too many attempts. Please wait a moment.'],
                'token'  => csrf_hash(),
            ]);
        }

        $in = $this->request->getJSON(true);
        if (!is_array($in)) {
            $in = (array) $this->request->getPost();
        }

        $placer = new OrderPlacer();
        $order  = $placer->place($in);

        if ($order === null) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'     => false,
                'errors' => $placer->errors(),
                'token'  => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'ok'    => true,
            'order' => $order,
            'token' => csrf_hash(),
        ]);
    }
}

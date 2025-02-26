<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;
    use ParametersTrait;

    protected ?string $redirectUrlSessionKey = null;

    /**
     * @param string $order
     * @return array|null
     */
    public function exchangeOrderStringToArray(string $order)
    {
        $arr = explode('||', $order);
        if (count($arr) !== 2) {
            return null;
        }

        return [$arr[0] => $arr[1]];
    }

    public function getOrderBySettings($request, string $name)
    {
        $order = $request->query->get('order', $request->session()->get('order_restaurant', 'id||desc'));

        if (is_string($order)) {
            $orderArr = explode('||', $order);
            if (count($orderArr) !== 2) {
                return null;
            }
            $order = [$orderArr[0] => $orderArr[1]];
        }

        return $order;
    }

    protected function getRefererInDomain($request)
    {
        $referer = $request->headers->get('referer');
        $domain = getenv('TENANCY_DEFAULT_HOSTNAME');
        if (strpos($referer, $domain) > 0) {
            return $referer;
        }

        return null;
    }

    protected function getRedirectUrl(Request $request, string $defaultUrl): string
    {
        $referer = $this->getRefererInDomain($request);
        $redirectUrl = old('redirect_url', $request->get('redirect_url'));

        if ($this->redirectUrlSessionKey) {
            $redirectUrl = $request->session()->get($this->redirectUrlSessionKey);
        }

        if ($redirectUrl) {
            return $redirectUrl;
        }
        if ($referer == null || $referer == $request->url()) {
            return $defaultUrl;
        }

        return $referer;
    }

    protected function redirectToIndex(Request $request, string $default = '', array $params = [])
    {
        return $request->has('redirect_url') ?
            redirect()->to(html_entity_decode($request->get('redirect_url'))) : redirect()->route($default, $params);
    }

    protected function hydrateData(array $data, $request)
    {
        $data['redirectUrl'] = $this->getRedirectUrl($request, $data['defaultRedirectUrl']);

        return $data;
    }
}

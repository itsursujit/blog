<?php namespace Modules\Gateway\Services;
/**
 * File RawPresenter
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Services
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
class RawPresenter implements PresenterContract
{
    /**
     * @param array|string $input
     * @param $code
     * @return Response
     */
    public function format($input, $code)
    {
        if (is_array($input)) $input = json_encode($input);

        return new Response($input, $code, [
            'Content-Type' => 'application/json'
        ]);
    }
}
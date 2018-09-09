<?php namespace Modules\Gateway\Contracts;
use Illuminate\Http\Response;

/**
 * File PresenterContract
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Contracts
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
interface PresenterContract
{
    /**
     * @param array|string $input
     * @param $code
     * @return Response
     */
    public function format($input, $code);

}
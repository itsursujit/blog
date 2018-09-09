<?php namespace Modules\Gateway\Exceptions;
use Illuminate\Http\Response;

/**
 * File DateFormatException
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Exceptions
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
class UnableToExecuteRequestException extends Exception
{
    /**
     * UnableToExecuteRequestException constructor.
     * @param Response $response
     */
    public function __construct(Response $response = null)
    {
        if ($response) {
            parent::__construct((string)$response->getBody(), $response->getStatusCode());
            return;
        }

        parent::__construct('Unable to finish the request', 502);
    }
}
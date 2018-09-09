<?php namespace Modules\Gateway\Services;
use Illuminate\Http\Response;
use Modules\Gateway\Contracts\PresenterContract;
use Modules\Gateway\Exceptions\DataFormatException;

/**
 * File JSONPresenter
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
class JSONPresenter implements PresenterContract
{
    /**
     * @param $input
     * @return array
     */
    public static function safeDecode($input) {
        // Fix for PHP's issue with empty objects
        $input = preg_replace('/{\s*}/', "{\"EMPTY_OBJECT\":true}", $input);

        return json_decode($input, true);
    }

    /**
     * @param array|object $input
     * @return string
     */
    public static function safeEncode($input) {
        return preg_replace('/{"EMPTY_OBJECT"\s*:\s*true}/', '{}', json_encode($input, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param array|string $input
     * @param              $code
     *
     * @return Response
     * @throws \Modules\Gateway\Exceptions\DataFormatException
     */
    public function format($input, $code)
    {
        if (empty($input) && ! is_array($input)) return new Response(null, $code);

        $serialized = is_array($input) ? $this->formatArray($input) : $this->formatString($input);

        return new Response($serialized, $code, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param $input
     * @return string
     * @throws DataFormatException
     */
    private function formatString($input)
    {
        $decoded = self::safeDecode($input);
        if ($decoded === null) throw new DataFormatException('Unable to decode input');

        return $this->formatArray($decoded);
    }

    /**
     * @param array|mixed $input
     * @return string
     */
    private function formatArray($input)
    {
        $output = [];

        if (is_array($input) && isset($input['error']) && is_string($input['error'])) {
            $output['errors'] = [ $input['error'] ];
            unset($input['error']);
        }

        if (is_array($input) && isset($input['errors']) && is_array($input['errors'])) {
            $output['errors'] = $input['errors'];
            unset($input['errors']);
        }

        $output['data'] = $input;

        return self::safeEncode($output);
    }
}
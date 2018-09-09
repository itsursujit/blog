<?php namespace Modules\Gateway\Contracts;
/**
 * File RouteContract
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
interface RouteContract
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @return bool
     */
    public function isPublic();

    /**
     * @return bool
     */
    public function isAggregate();

    /**
     * @return Collection
     */
    public function getActions();

    /**
     * @return PresenterContract
     */
    public function getPresenter();

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param ActionContract $action
     * @return $this
     */
    public function addAction(ActionContract $action);

}
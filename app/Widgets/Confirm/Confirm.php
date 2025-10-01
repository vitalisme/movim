<?php

namespace App\Widgets\Confirm;

use App\Widgets\Dialog\Dialog;
use Movim\Widget\Base;

use Moxl\Xec\Action\Confirm\Accept;
use Moxl\Xec\Action\Confirm\Refuse;
use Moxl\Xec\Payload\Packet;

class Confirm extends Base
{
    public function load()
    {
        $this->addcss('confirm.css');
        $this->registerEvent('confirm', 'onConfirm');
    }

    public function onConfirm(Packet $packet)
    {
        $view = $this->tpl();

        $view->assign('from', $packet->content['from']);
        $view->assign('id', $packet->content['id']);
        $view->assign('url', $packet->content['url']);
        $view->assign('method', $packet->content['method']);

        Dialog::fill($view->draw('_confirm'));
    }

    public function ajaxAccept($to, $id, $url, $method)
    {
        $accept = new Accept;
        $accept->setTo($to)
               ->setId($id)
               ->setUrl($url)
               ->setMethod($method)
               ->request();
    }

    public function ajaxRefuse($to, $id, $url, $method)
    {
        $refuse = new Refuse;
        $refuse->setTo($to)
               ->setId($id)
               ->setUrl($url)
               ->setMethod($method)
               ->request();
    }
}

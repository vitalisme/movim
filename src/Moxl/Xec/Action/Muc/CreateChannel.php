<?php

namespace Moxl\Xec\Action\Muc;

use Moxl\Xec\Action;
use Moxl\Stanza\Muc;

class CreateChannel extends Action
{
    protected $_to;
    protected $_name;
    protected $_nick;
    protected $_autojoin;
    protected $_notify;
    protected $_pinned;

    public function request()
    {
        $this->store();
        Muc::createChannel($this->_to, $this->_name);
    }

    public function handle(?\SimpleXMLElement $stanza = null, ?\SimpleXMLElement $parent = null)
    {
        $this->pack([
            'jid' => $this->_to,
            'name' => $this->_name,
            'nick' => $this->_nick,
            'autojoin' => $this->_autojoin,
            'pinned' => $this->_pinned,
            'notify' => $this->_notify,
        ]);
        $this->deliver();
    }

    public function error(string $errorId, ?string $message = null)
    {
        if ($message) {
            $this->pack($message);
            $this->deliver();
        }
    }
}

<?php

namespace App\Widgets\AccountNext;

use App\Widgets\Toast\Toast;
use Movim\Librairies\XMPPtoForm;
use Moxl\Xec\Action\Register\Set;

use Movim\Session;
use Moxl\Xec\Payload\Packet;

class AccountNext extends \Movim\Widget\Base
{
    public function load()
    {
        $this->addjs('accountnext.js');
        $this->addcss('accountnext.css');

        $this->registerEvent('register_get_handle', 'onForm');
        $this->registerEvent('register_set_handle', 'onRegistered');
        $this->registerEvent('register_set_errorconflict', 'onRegisterError', 'accountnext');
        $this->registerEvent('register_set_errorforbidden', 'onForbidden', 'accountnext');
        $this->registerEvent('register_set_errornotacceptable', 'onRegisterNotAcceptable', 'accountnext');
        $this->registerEvent('register_get_errorserviceunavailable', 'onServiceUnavailable', 'accountnext');
    }

    public function onForm(Packet $packet)
    {
        $form = $packet->content;

        $xtf = new XMPPtoForm;
        $html = '';
        if (!empty($form->x)) {
            switch ($form->x->attributes()->xmlns) {
                case 'jabber:x:data':
                    $formview = $this->tpl();
                    $formview->assign('formh', $xtf->getHTML($form->x, $form));
                    $html = $formview->draw('_accountnext_form');
                    break;
                case 'jabber:x:oob':
                    $this->rpc('MovimUtils.redirect', (string)$form->x->url);
                    break;
            }
        } else {
            $formview = $this->tpl();
            $formview->assign('formh', $xtf->getHTML($form));
            $html = $formview->draw('_accountnext_form');
        }

        $this->rpc('MovimTpl.fill', '#subscription_form', $html);
    }

    public function onRegistered(Packet $packet)
    {
        $view = $this->tpl();
        $this->rpc('MovimTpl.fill', '#subscribe', $view->draw('_accountnext_registered'));
    }

    public function onError()
    {
        Toast::send($this->__('error.service_unavailable'));
    }

    public function onRegisterError(Packet $packet)
    {
        $error = $packet->content;
        Toast::send($error);
    }

    public function onForbidden()
    {
        Toast::send($this->__('error.forbidden'));
    }

    public function onRegisterNotAcceptable()
    {
        Toast::send($this->__('error.not_acceptable'));
    }

    public function onServiceUnavailable()
    {
        Toast::send($this->__('error.service_unavailable'));

        requestAPI('disconnect', post: ['sid' => SESSION_ID]);

        $this->rpc('MovimUtils.redirect', $this->route('account'));
    }

    public function ajaxGetForm($host)
    {
        global $dns;
        $domain = $host;

        $dns->resolveAll('_xmpp-client._tcp.' . $host, \React\Dns\Model\Message::TYPE_SRV)
        ->then(function ($resolved) use ($host, &$domain) {
            $domain = $resolved[0]['target'];
        })->always(function () use ($host, &$domain) {
            // We create a new session or clear the old one
            $session = Session::instance();
            $session->set('host', $host);
            $session->set('domain', $domain);

            \Moxl\Stanza\Stream::init($host);
        });
    }

    public function ajaxRegister($form)
    {
        if (isset($form->re_password)
        && $form->re_password->value != $form->password->value) {
            Toast::send($this->__('account.password_not_same'));
            return;
        }

        $s = new Set;
        $s->setData(formToArray($form))
          ->request();
    }

    public function display()
    {
        $host = $this->get('s');
        $this->view->assign('host', $host);
    }
}

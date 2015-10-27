<?php
namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\ActionController;
use Admin\Form\Login;

/**
 * Controlador que gerencia os posts
 * 
 * @category Admin
 * @package Controller
 * @author  Elton Minetto<eminetto@coderockr.com>
 */
class AuthController extends ActionController
{
    /**
     * Mostra o formulário de login
     * @return void
     */
    public function indexAction()
    {
        $form = new Login();
        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * Faz o login do usuário
     * @return void
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new \Exception('Acesso inválido');
        }

        $data = $request->getPost();
        $service = $this->getService('Admin\Service\Auth');
        $auth = $service->authenticate(
            array('username' => $data['username'], 'password' => $data['password'])
        );
        
        return $this->redirect()->toUrl('/');
    }

    /**
     * Faz o logout do usuário
     * @return void
     */
    public function logoutAction()
    {
        $service = $this->getService('Admin\Service\Auth');
        $auth = $service->logout();
        
        return $this->redirect()->toUrl('/');
    }
}
<?php

namespace Application\Controller;

use Core\Controller\ActionController;
use Zend\View\Model\ViewModel;

class IndexController extends ActionController
{

    /**
     * Mostra os posts cadastrados
     * @return void
     */
    public function indexAction() {
        return new ViewModel(array(
            'posts' => $this->getTable('Application\Model\Post')->fetchAll()->toArray()
        ));
    }

    public function postAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id == 0) {
            throw new \Exception("Código obrigatório");
        }

        // Selecionar Post
        $post = $this->getTable('Application\Model\Post')
                ->get($id)
                ->toArray()
                ;
        
        $post['comentarios'] = $this->getTable('Application\Model\Comment')
                ->fetchAll(null, "post_id = $id")
                ->toArray()
                ;

        return new ViewModel(array(
            'post' => $post,
        ));
    }

}

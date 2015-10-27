<?php

namespace Application\Controller;

use Core\Controller\ActionController;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class IndexController extends ActionController
{

    /**
     * Mostra os posts cadastrados
     * @return void
     */
    public function indexAction() {
        $post = $this->getTable('Application\Model\Post');
        $sql = $post->getSql();
        $select = $sql->select();

        $paginatorAdapter = new DbSelect($select, $sql);
        $paginator = new Paginator($paginatorAdapter);
        // $paginator->setCache($this->getServiceLocator()->get('Cache'));
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        
        return new ViewModel(array(
            'posts' => $paginator
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

<?php

namespace Application\Controller;

use Core\Controller\ActionController;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class IndexController extends ActionController {

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

    /**
     * Retorna os comentarios de um post
     * 
     * @return Zend\Http\Response
     */
    public function commentsAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        $where = array(
            'post_id' => $id,
        );

        $comments = $this->getTable('Application\Model\Comment')
                ->fetchAll(null, $where)
                ->toArray()
        ;

        // Retorno em JSON
        if (false) {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(json_encode($comments));

            $response->getHeaders()->addHeaderLine('Content-type', 'application/json');
            return $response;
        }
        
        // Retorno sem Layout
        if (true) {
            $result = new ViewModel(array(
                'comments' => $comments,
            ));
            $result->setTerminal(true); // sem layout
            return $result;
        }
    }

}

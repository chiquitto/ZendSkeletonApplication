<?php

use Application\Model\Comment;
use Application\Model\Post;
use Core\Test\ControllerTestCase;

/**
 * @group Controller
 */
class IndexControllerTest extends ControllerTestCase
{

    /**
     * Namespace completa do Controller
     * @var string
     */
    protected $controllerFQDN = 'Application\Controller\IndexController';

    /**
     * Nome da rota. Geralmente o nome do módulo
     * @var string
     */
    protected $controllerRoute = 'application';

    /**
     * Testa o acesso a uma action que não existe
     */
    public function test404() {
        $this->routeMatch->setParam('action', 'action_nao_existente');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Testa a página inicial, que deve mostrar os posts
     */
    public function testIndexAction() {
        // Cria posts para testar
        $postA = $this->addPost();
        $postB = $this->addPost();

        // Invoca a rota index
        $this->routeMatch->setParam('action', 'index');
        $result = $this->controller->dispatch($this->request, $this->response);

        // Verifica o response
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // Testa se um ViewModel foi retornado
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);

        // Testa os dados da view
        $variables = $result->getVariables();

        $this->assertArrayHasKey('posts', $variables);

        // Faz a comparação dos dados
        $controllerData = $variables["posts"]->getCurrentItems()->toArray();

        $this->assertEquals($postA->title, $controllerData[0]['title']);
        $this->assertEquals($postB->title, $controllerData[1]['title']);
    }

    /**
     * Testa a página inicial, que deve mostrar os posts com paginador
     */
    public function testIndexPaginatorAction() {
        // Cria posts para testar
        $post = array();
        for ($i = 0; $i < 25; $i++) {
            $post[] = $this->addPost();
        }

        // Invoca a rota index
        $this->routeMatch->setParam('action', 'index');
        $result = $this->controller->dispatch($this->request, $this->response);

        // Verifica o response
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // Testa se um ViewModel foi retornado
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);

        // Testa os dados da view
        $variables = $result->getVariables();

        $this->assertArrayHasKey('posts', $variables);

        //testa o paginator
        $paginator = $variables["posts"];
        
        $this->assertEquals('Zend\Paginator\Paginator', get_class($paginator));
        $this->assertInstanceOf('Zend\Paginator\Paginator', $paginator);
        
        $posts = $paginator->getCurrentItems()->toArray();
        
        $this->assertEquals(10, count($posts));
        $this->assertEquals($post[0]->id, $posts[0]['id']);
        $this->assertEquals($post[1]->id, $posts[1]['id']);

        //testa a terceira página da paginação
        $this->routeMatch->setParam('action', 'index');
        $this->routeMatch->setParam('page', 3);
        $result = $this->controller->dispatch($this->request, $this->response);
        $variables = $result->getVariables();
        $controllerData = $variables["posts"]->getCurrentItems()->toArray();
        $this->assertEquals(5, count($controllerData));
    }

    /**
     * Testa a pagina post
     */
    public function testPostAction() {
        $postA = $this->addPost();

        $commentA = $this->addComment($postA);
        $commentB = $this->addComment($postA);
        $commentC = $this->addComment($postA);

        // Invoca a rota comentarios
        $this->routeMatch->setParam('action', 'post');
        $this->routeMatch->setParam('id', $postA->id);
        $result = $this->controller->dispatch($this->request, $this->response);

        // Verifica o response
        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // Testa se um ViewModel foi retornado
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);

        // Testa os dados da view
        $variables = $result->getVariables();
        $this->assertArrayHasKey('post', $variables);

        // Comparacao do id do post
        $postView = $variables["post"];
        $this->assertEquals($postA->title, $postView['title']);

        $this->assertEquals(3, count($postView['comentarios']));

        // Faz a comparacao do id do comentario
        $this->assertEquals($commentA->id, $postView['comentarios'][0]['id']);
    }

    /**
     * Test post page without id parameter
     * @expectedException Exception
     */
    public function testPostSemIdAction() {
        // Invoca a rota
        $this->routeMatch->setParam('action', 'post');
        $result = $this->controller->dispatch($this->request, $this->response);
    }

    /**
     * Test post page without id parameter
     * @expectedException Exception
     */
    public function testPostIdInvalidoAction() {
        // Invoca a rota
        $this->routeMatch->setParam('action', 'post');
        $this->routeMatch->setParam('id', 100);
        $result = $this->controller->dispatch($this->request, $this->response);
    }

    private function addComment(Post $post) {
        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->description = 'Nossa que ******';
        $comment->name = 'Alisson Chiquitto';
        $comment->email = 'chiquitto@gmail.com';
        $comment->webpage = 'www.chiquitto.com.br';
        $comment->comment_date = date('Y-m-d H:i:s');

        $saved = $this->getTable('Application\Model\Comment')->save($comment);

        return $saved;
    }

    /**
     * Adiciona um post para os testes
     */
    private function addPost() {
        $post = new Post();
        $post->title = 'Apple compra a Coderockr';
        $post->description = 'A Apple compra a <b>Coderockr</b><br> ';
        $post->post_date = date('Y-m-d H:i:s');

        $saved = $this->getTable('Application\Model\Post')->save($post);

        return $saved;
    }

}

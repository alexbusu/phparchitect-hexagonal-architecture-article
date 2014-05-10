<?php
class RedisIdeaRepository implements IdeaRepository
{
    private $client;

    public function __construct()
    {
        $this->client = new \Predis\Client();
    }

    /**
     * @param int $id
     * @return null
     */
    public function find($id)
    {
        $idea = $this->client->get('idea_'.$id);
        if (!$idea) {
            return null;
        }

        return $idea;
    }

    /**
     * @param Idea $idea
     */
    public function update($idea)
    {
        $this->client->set('idea_'.$idea->getId(), $idea);
    }
}

class IdeaController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $ideaId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $ideaRepository = new RedisIdeaRepository();
        $idea = $ideaRepository->find($ideaId);
        if (!$idea) {
            throw new Exception('Idea does not exist');
        }

        $idea->addVote($rating);
        $ideaRepository->save($idea);

        $this->redirect('/idea/'.$ideaId);
    }
}

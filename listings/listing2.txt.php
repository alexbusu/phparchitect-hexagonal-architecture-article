<?php

/**
 * Class IdeaRepository
 */
class IdeaRepository
{
    private $client;

    public function __construct()
    {
        $this->client = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => 'localhost',
            'username' => 'idy',
            'password' => '',
            'dbname'   => 'idy'
        ));
    }

    /**
     * Finds an idea by id
     *
     * @param int $id
     * @return null|Idea
     */
    public function find($id)
    {
        $sql = 'SELECT * FROM ideas WHERE idea_id = ?';
        $row = $this->client->fetchRow($sql, $id);
        if (!$row) {
            return null;
        }

        $idea = new Idea();
        $idea->setId($row['id']);
        $idea->setTitle($row['title']);
        $idea->setDescription($row['description']);
        $idea->setRating($row['rating']);
        $idea->setVotes($row['votes']);
        $idea->setAuthor($row['email']);

        return $idea;
    }

    /**
     * @param Idea $idea
     * @throws Zend_Db_Adapter_Exception
     */
    public function update($idea)
    {
        $data = array(
            'title' => $idea->getTitle(),
            'description' => $idea->getDescription(),
            'rating' => $idea->getRating(),
            'votes' => $idea->getVotes(),
            'email' => $idea->getAuthor(),
        );

        $where = array('idea_id = ?' => $idea->getId());
        $this->client->update('ideas', $data, $where);
    }
}

/**
 * Class IdeaController
 */
class IdeaController extends Zend_Controller_Action
{
    public function voteAction()
    {
        $ideaId = $this->request->getParam('id');
        $rating = $this->request->getParam('rating');

        $ideaRepository = new IdeaRepository();
        $idea = $ideaRepository->find($ideaId);
        if (!$idea) {
            throw new Exception('Idea does not exist');
        }

        $idea->addVote($rating);
        $ideaRepository->update($idea);

        $this->redirect('/idea/'.$ideaId);
    }
}

<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * Studies Controller
 *
 * @property \App\Model\Table\StudiesTable $Studies
 *
 * @method \App\Model\Entity\Study[] paginate($object = null, array $settings = [])
 */
class StudiesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $studies = $this->paginate($this->Studies);

        $this->set(compact('studies'));
        $this->set('_serialize', ['studies']);
    }

    /**
     * View method
     *
     * @param string|null $id Study id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $study = $this->Studies->get($id, [
            'contain' => ['Users', 'Rounds']
        ]);

        $this->set('study', $study);
        $this->set('_serialize', ['study']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $study = $this->Studies->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            debug($data);
            $study = $this->Studies->patchEntity($study, $data,['associated' => ['Users','Rounds','Rounds.QuestionsIndicatorsYears']]);
            if ($this->Studies->save($study)) {
                $this->Flash->success(__('The study has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The study could not be saved. Please, try again.'));
        }
        $users = $this->Studies->Users->find('list',['keyField' => 'id', 'valueField' => 'username']);
        $questions = $this->Studies->Rounds->QuestionsIndicatorsYears->QuestionsIndicators->Questions->find('list',['keyField' => 'id','valueField' => 'description']);
        $this->set(compact('study', 'users','questions','questions_indicators_values'));
        $this->set('_serialize', ['study','questions','questions_indicators_values']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Study id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $study = $this->Studies->get($id, [
            'contain' => ['Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $study = $this->Studies->patchEntity($study, $this->request->getData());
            if ($this->Studies->save($study)) {
                $this->Flash->success(__('The study has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The study could not be saved. Please, try again.'));
        }
        $users = $this->Studies->Users->find('list', ['limit' => 200]);
        $this->set(compact('study', 'users'));
        $this->set('_serialize', ['study']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Study id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $study = $this->Studies->get($id);
        if ($this->Studies->delete($study)) {
            $this->Flash->success(__('The study has been deleted.'));
        } else {
            $this->Flash->error(__('The study could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function finish($id = null)
    {
        $this->request->allowMethod('post');
        $study = $this->Studies->get($id);

        $study->completed = Time::now();

        if($this->Studies->save($study))
        {
            $this->Flash->success(__('The study has been finished '));
        } else {
            $this->Flash->error(__('The study could not be finished'. 'Please, try again'));
        }

        return $this->redirect(['action' => 'index']);
    }


    public function addRound($id = null)
    {
        $lastRound = $this->Studies->getLastRound($id);
        /*if(!$lastRound->completed)
        {
            $this->Flash->error(__('Last Round has not been finished'));
            return $this->redirect(['action' => 'view', $lastRound->study_id]);
        }*/

        $round = $this->Studies->Rounds->newEntity();

        $study_id = $id;
        $step = $lastRound->step + 1;


        $indicatorsYears = $this->Studies->Rounds->getQuestionsIndicatorsYears($lastRound->id);

        if($this->request->is('post'))
        {
            $data = $this->request->getData();
            $round = $this->Studies->Rounds->patchEntity($round,$data);
            if($this->Studies->Rounds->save($round))
            {
                $this->Flash->success(__('The round has been saved'));

                echo json_encode($round);
                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The round could not be saved'));
        }


        $this->set(compact('indicatorsYears','step','round','study_id'));
        $this->set('_serialize',['indicatorsYears','step','round','study_id']);
    }
}

<?php
namespace App\Model\Table;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Studies Model
 *
 * @property \App\Model\Table\RoundsTable|\Cake\ORM\Association\HasMany $Rounds
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsToMany $Users
 *
 * @method \App\Model\Entity\Study get($primaryKey, $options = [])
 * @method \App\Model\Entity\Study newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Study[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Study|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Study patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Study[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Study findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StudiesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('studies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Rounds', [
            'foreignKey' => 'study_id'
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'study_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_studies'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('description');

        $validator
            ->dateTime('completed')
            ->allowEmpty('completed');

        $validator
            ->requirePresence('category', 'create')
            ->notEmpty('category');

        $validator
            ->requirePresence('scenario', 'create')
            ->notEmpty('scenario');

        return $validator;
    }


    public function getActiveRound($study_id = null)
    {
        $round = $this->Rounds->find('all', ['conditions' => ['study_id' => $study_id, 'Rounds.completed is null'], 'order' => ['id' => 'ASC']])->first();

        if(empty($round))
        {
            throw new RecordNotFoundException('Round not found');
        }

        return $round;
    }

    public function hasRounds($id = null)
    {

        return $this->Rounds->exists(['study_id' => $id]);
    }

    public function getLastRound($id = null)
    {
        return $this->Rounds->find('all', ['conditions' => ['study_id' => $id], 'order' => ['Rounds.id' => 'DESC']])->first();

    }
}

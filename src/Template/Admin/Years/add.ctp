<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Year $year
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Years'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Questions Indicators'), ['controller' => 'QuestionsIndicators', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Questions Indicator'), ['controller' => 'QuestionsIndicators', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="years form large-9 medium-8 columns content">
    <?= $this->Form->create($year) ?>
    <fieldset>
        <legend><?= __('Add Year') ?></legend>
        <?php
            echo $this->Form->control('description', ['type' => 'text']);
            //echo $this->Form->control('questions_indicators._ids', ['options' => $questionsIndicators]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>

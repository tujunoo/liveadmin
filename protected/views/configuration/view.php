<?php
$this->breadcrumbs=array(
        'Configurations'=>array('index'),
        $model->configuration_id,
);

$this->menu=array(
        array('label'=>'List Configuration', 'url'=>array('index')),
        array('label'=>'Create Configuration', 'url'=>array('create')),
        array('label'=>'Update Configuration', 'url'=>array('update', 'id'=>$model->configuration_id)),
        array('label'=>'Delete Configuration', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->configuration_id),'confirm'=>'Are you sure you want to delete this item?')),
        array('label'=>'Manage Configuration', 'url'=>array('admin')),
);
?>

<h1>View Configuration #<?php echo $model->configuration_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
        'data'=>$model,
        'attributes'=>array(
                'configuration_id',
                'configuration_title',
                'configuration_key',
                'configuration_value',
                'configuration_description',
                'configuration_group_id',
                'sort_order',
                'last_modified',
                'date_added',
                'use_function',
                'set_function',
        ),
)); ?>

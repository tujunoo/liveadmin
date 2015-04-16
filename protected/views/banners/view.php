<?php
$this->breadcrumbs=array(
        'Banners'=>array('index'),
        $model->banners_id,
);

$this->menu=array(
        array('label'=>'List Banners', 'url'=>array('index')),
        array('label'=>'Create Banners', 'url'=>array('create')),
        array('label'=>'Update Banners', 'url'=>array('update', 'id'=>$model->banners_id)),
        array('label'=>'Delete Banners', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->banners_id),'confirm'=>'Are you sure you want to delete this item?')),
        array('label'=>'Manage Banners', 'url'=>array('admin')),
);
?>

<h1>View Banners #<?php echo $model->banners_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
        'data'=>$model,
        'attributes'=>array(
                'banners_id',
                'banners_title',
                'banners_url',
                'banners_image',
                'banners_group',
                'banners_html_text',
                'expires_impressions',
                'expires_date',
                'date_scheduled',
                'date_added',
                'date_status_change',
                'status',
                'banner_sort_order',
        ),
)); ?>

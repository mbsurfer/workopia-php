<?php loadPartial('head'); ?>
<?php loadPartial('navbar'); ?>
<?php loadPartial('top-banner'); ?>

<?php loadView('listings/form', [
    'listing' => $listing,
    'title' => 'Create Job Listing',
    'errors' => $errors ?? [],
    'action' => '/listings',
    'cancel_action' => '/'
]) ?>

<?php loadPartial('bottom-banner'); ?>
<?php loadPartial('footer'); ?>
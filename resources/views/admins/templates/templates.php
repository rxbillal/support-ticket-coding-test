<script id="adminActionTemplate" type="text/x-jsrender">
   {{if id != 1 }}
   <a title="<?php echo __('messages.common.edit') ?>
    " class="btn btn-warning action-btn edit-btn" data-id="{{:id}}" href="{{:url}}">
            <i class="fa fa-edit"></i>
   </a>
   <a  title="<?php echo __('messages.common.delete') ?>
    " class="btn btn-danger action-btn admin-delete-btn" data-id="{{:id}}" href="#">
            <i class="fa fa-trash"></i>
   </a>
   {{/if}}








</script>

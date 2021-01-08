<?php /*
  @Description: Category Add/Edit
  @Author: Dhaval Panchal
  @Date: 7-1-2021

 */ ?>
<?php
$viewname = $this->router->uri->segments[2];
$formAction = !empty($editRecord) ? 'update_data' : 'insert_data';
$path = $viewname . '/' . $formAction;
$is_edit = !empty($editRecord) ? "Edit Category" : "Add Category";
$edit_data = !empty($editRecord) ? '1' : '';
?>

<div class="main-container">
    <div class="page-container">
      <div class="titlebar">
        <h1 class="orange-clr"><?= $is_edit ?></h1>
        <a class="btn btn-primary pull-right" href="<?= base_url('admin/').$viewname;?>" title="Back">Back</a>  
      </div>
      <!-- add new user start -->
      <form action="<?= base_url().'admin/'.$path; ?>" class="form" method="post" id="<?= $viewname ?>" enctype="multipart/form-data" accept-charset="utf-8">
      <div class="card-block form-container">
        <div class="row">
          <div class="col-md-12 col-sm-6">
            <div class="row">
              <!-- <div class="col-md-4 col-sm-6">
                <div class="form-group custom-group">
                <label for=""><?= $this->lang->line('parent_category') ?>
                  <div class="select-style tdy-tsk-slct actn-slct">
                    <select id="parent_id" name="parent_id" >
                      <option value="">Select</option>
                      <option value=""></option>
                    </select>
                  </div>
                </div>
              </div> -->
              <div class="col-md-4 col-sm-6">
                <div class="form-group custom-group">
                  <label for=""><?= $this->lang->line('category_name') ?></label><span style="color:#F00">*</span>
                  <input id="name" name="name" minlength="2" maxlength="100" class="form-control" type="text"  value="<?php echo!empty(set_value('name')) ? set_value('name') : (!empty($editRecord[0]['name']) ? $editRecord[0]['name'] : ''); ?>">
                  <?php echo form_error('name'); ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 text-right">
                <div class="button-grp">
                  <input type="hidden" name="id" id="id" value="<?= (!empty($editRecord)) ? $editRecord[0]['id'] : ""; ?>">
                  <?php if(!empty($editRecord)) { ?>
                    <button type="submit" value="submitForm" name="save" class="btn btn-blue">Update</button>
                  <?php } else { ?>
                    <button type="submit" value="submitForm" name="save" class="btn btn-blue">Create</button>
                    <button class="btn btn-gray">Reset</button>
                  <?php }?>
                </div>
              </div>
            </div>
          </div>
      </div>
      </form>
    </div>
    <!-- add new user end -->
  </div>
</div>

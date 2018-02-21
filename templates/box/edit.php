<?php
/**
 * @package Packer
 * @author Iurii Makukh
 * @copyright Copyright (c) 2018, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 * @var $this \gplcart\core\controllers\backend\Controller
 * To see available variables <?php print_r(get_defined_vars()); ?>
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Status'); ?></label>
    <div class="col-md-4">
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-default<?php echo empty($box['status']) ? '' : ' active'; ?>">
          <input name="box[status]" type="radio" autocomplete="off" value="1"<?php echo empty($box['status']) ? '' : ' checked'; ?>><?php echo $this->text('Enabled'); ?>
        </label>
        <label class="btn btn-default<?php echo empty($box['status']) ? ' active' : ''; ?>">
          <input name="user[status]" type="radio" autocomplete="off" value="0"<?php echo empty($box['status']) ? ' checked' : ''; ?>><?php echo $this->text('Disabled'); ?>
        </label>
      </div>
      <div class="help-block">
        <?php echo $this->text('Disabled boxed will not be available for package calculation during checkout'); ?>
      </div>
    </div>
  </div>
  <div class="form-group required<?php echo $this->error('name', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Name'); ?></label>
    <div class="col-md-4">
      <input maxlength="255" name="box[name]" class="form-control" value="<?php echo isset($box['name']) ? $this->e($box['name']) : ''; ?>">
      <div class="help-block">
          <?php echo $this->error('name'); ?>
        <div class="text-muted">
            <?php echo $this->text('The name for administrators'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group <?php echo $this->error('shipping_method', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Shipping method'); ?></label>
    <div class="col-md-4">
      <input maxlength="255" name="box[shipping_method]" class="form-control" placeholder="<?php echo $this->text('Any'); ?>" value="<?php echo isset($box['shipping_method']) ? $this->e($box['shipping_method']) : ''; ?>">
      <div class="help-block">
          <?php echo $this->error('shipping_method'); ?>
        <div class="text-muted">
            <?php echo $this->text('Assign a <a href="@url">shipping method</a> ID for the box type. Wildcards <code>*</code> and <code>?</code> are supported. Leave empty to allow all shipping methods', array('@url' => $this->url('admin/report/shipping'), '@url2' => 'https://dev.mysql.com/doc/refman/5.7/en/pattern-matching.html')); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-10 col-md-offset-2">
      <?php echo $this->text('Box dimensions. Used to calculate number of boxes needed to fit all the items'); ?>
    </div>
  </div>
  <div class="form-group required<?php echo $this->error('size_unit', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Size unit'); ?></label>
    <div class="col-md-4">
      <select name="box[size_unit]" class="form-control">
          <?php foreach ($size_units as $unit => $unit_name) { ?>
            <option value="<?php echo $unit; ?>"<?php echo isset($box['size_unit']) && $box['size_unit'] == $unit ? ' selected' : ''; ?>><?php echo $this->e($unit_name); ?></option>
          <?php } ?>
      </select>
      <div class="help-block">
          <?php echo $this->error('size_unit'); ?>
      </div>
    </div>
  </div>
  <div class="form-group required<?php echo $this->error('weight_unit', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Weight unit'); ?></label>
    <div class="col-md-4">
      <select name="box[weight_unit]" class="form-control">
          <?php foreach ($weight_units as $unit => $unit_name) { ?>
            <option value="<?php echo $unit; ?>"<?php echo isset($box['weight_unit']) && $box['weight_unit'] == $unit ? ' selected' : ''; ?>><?php echo $this->e($unit_name); ?></option>
          <?php } ?>
      </select>
      <div class="help-block">
          <?php echo $this->error('weight_unit'); ?>
      </div>
    </div>
  </div>
  <?php foreach($fields as $field => $name) { ?>
    <div class="form-group required<?php echo $this->error($field, ' has-error'); ?>">
      <label class="col-md-2 control-label"><?php echo $this->text($name); ?></label>
      <div class="col-md-4">
        <input maxlength="255" name="box[<?php echo $field; ?>]" class="form-control" value="<?php echo isset($box[$field]) ? $this->e($box[$field]) : ''; ?>">
        <div class="help-block">
            <?php echo $this->error($field); ?>
        </div>
      </div>
    </div>
  <?php } ?>
  <div class="row">
    <div class="col-md-10 col-md-offset-2">
      <div class="btn-toolbar">
        <?php if (isset($box['box_id']) && $this->access('module_packer_box_delete')) { ?>
        <button class="btn btn-danger delete" name="delete" value="1" onclick="return confirm('<?php echo $this->text('Are you sure? It cannot be undone!'); ?>');">
          <?php echo $this->text('Delete'); ?>
        </button>
        <?php } ?>
        <a href="<?php echo $this->url('admin/settings/box'); ?>" class="btn btn-default"><?php echo $this->text('Cancel'); ?></a>
        <?php if ($this->access('module_packer_box_add') || $this->access('module_packer_box_edit')) { ?>
        <button class="btn btn-default save" name="save" value="1"><?php echo $this->text('Save'); ?></button>
        <?php } ?>
      </div>
    </div>
  </div>
</form>

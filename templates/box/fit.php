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
  <?php if(!empty($items)) { ?>
    <div class="form-group">
      <label class="col-md-2 control-label"><?php echo $this->text('Will fit'); ?></label>
      <div class="col-md-10">
        <table class="table table-bordered">
          <thead>
          <tr>
            <td><?php echo $this->text('ID'); ?></td>
            <td><?php echo $this->text('Product'); ?></td>
            <td><?php echo $this->text('Width'); ?></td>
            <td><?php echo $this->text('Length'); ?></td>
            <td><?php echo $this->text('Height'); ?></td>
            <td><?php echo $this->text('Weight'); ?></td>
          </tr>
          </thead>
          <tbody>
          <?php foreach($items as $item) { ?>
            <tr>
              <td><?php echo $this->e($item['product_id']); ?></td>
              <td><?php echo $this->e($item['title']); ?></td>
              <td><?php echo $this->e("{$item['width']}{$item['size_unit']}"); ?></td>
              <td><?php echo $this->e("{$item['length']}{$item['size_unit']}"); ?></td>
              <td><?php echo $this->e("{$item['height']}{$item['size_unit']}"); ?></td>
              <td><?php echo $this->e("{$item['weight']}{$item['weight_unit']}"); ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php } ?>
  <div class="form-group required<?php echo $this->error('product_id', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Products'); ?></label>
    <div class="col-md-4">
      <textarea name="box[product_id]" class="form-control"><?php echo isset($box['product_id']) ? $this->e($box['product_id']) : ''; ?></textarea>
      <div class="help-block">
          <?php echo implode('<br>', (array) $this->error('product_id', null, array())); ?>
        <div class="text-muted">
            <?php echo $this->text('Specify ID of products you want to fit into the box. One ID per line'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-10 col-md-offset-2">
      <div class="btn-toolbar">
        <a href="<?php echo $this->url('admin/settings/box'); ?>" class="btn btn-default"><?php echo $this->text('Cancel'); ?></a>
        <button class="btn btn-default" name="fit" value="1"><?php echo $this->text('Fit'); ?></button>
      </div>
    </div>
  </div>
</form>

<?php
/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 * @var $this \gplcart\core\controllers\backend\Controller
 * To see available variables <?php print_r(get_defined_vars()); ?>
 */
?>
<?php if($this->access('module_packer_box_add')) { ?>
  <div class="btn-toolbar actions">
      <a class="btn btn-default" href="<?php echo $this->url('admin/settings/box/add'); ?>">
          <?php echo $this->text('Add'); ?>
      </a>
  </div>
<?php } ?>
<?php if (!empty($boxes)) { ?>
<form method="post">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
<?php if ($this->access('module_packer_box_delete')) { ?>
<div class="form-inline actions">
  <div class="input-group">
    <select name="action[name]" class="form-control" onchange="Gplcart.action(this);">
      <option value=""><?php echo $this->text('With selected'); ?></option>
      <option value="delete" data-confirm="<?php echo $this->text('Are you sure? It cannot be undone!'); ?>">
        <?php echo $this->text('Delete'); ?>
      </option>
    </select>
    <span class="input-group-btn hidden-js">
      <button class="btn btn-default" name="action[submit]" value="1"><?php echo $this->text('OK'); ?></button>
    </span>
  </div>
</div>
<?php } ?>
<div class="table-responsive">
  <table class="table">
    <thead>
      <tr>
        <th><input type="checkbox" onchange="Gplcart.selectAll(this);"></th>
        <th><a href="<?php echo $sort_box_id; ?>"><?php echo $this->text('ID'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_name; ?>"><?php echo $this->text('Name'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_shipping_method; ?>"><?php echo $this->text('Shipping method'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_inner_width; ?>"><?php echo $this->text('Inner width'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_inner_length; ?>"><?php echo $this->text('Inner length'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_inner_depth; ?>"><?php echo $this->text('Inner depth'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_max_weight; ?>"><?php echo $this->text('Max weight'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_weight_unit; ?>"><?php echo $this->text('Weight unit'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_size_unit; ?>"><?php echo $this->text('Size unit'); ?> <i class="fa fa-sort"></i></a></th>
        <th><a href="<?php echo $sort_status; ?>"><?php echo $this->text('Enabled'); ?> <i class="fa fa-sort"></i></a></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($boxes as $box) { ?>
      <tr>
        <td class="middle">
          <input type="checkbox" class="select-all" name="action[items][]" value="<?php echo $box['box_id']; ?>">
        </td>
        <td class="middle"><?php echo $this->e($box['box_id']); ?></td>
        <td class="middle"><?php echo $this->e($this->truncate($box['name'], 50)); ?></td>
        <td class="middle"><?php echo $this->e($this->truncate($box['shipping_method'], 50)); ?></td>
        <td class="middle"><?php echo $this->e($box['inner_width']); ?></td>
        <td class="middle"><?php echo $this->e($box['inner_length']); ?></td>
        <td class="middle"><?php echo $this->e($box['inner_depth']); ?></td>
        <td class="middle"><?php echo $this->e($box['max_weight']); ?></td>
        <td class="middle"><?php echo $this->e($box['weight_unit']); ?></td>
        <td class="middle"><?php echo $this->e($box['size_unit']); ?></td>
        <td class="middle"><?php echo empty($box['status']) ? $this->text('No') : $this->text('Yes'); ?></td>
        <td class="middle">
          <ul class="list-inline">
            <?php if ($this->access('module_packer_box_edit')) { ?>
            <a href="<?php echo $this->url("admin/settings/box/edit/{$box['box_id']}"); ?>">
              <?php echo $this->lower($this->text('Edit')); ?>
            </a>
            <?php } ?>
              <?php if ($this->access('module_packer_box_fit')) { ?>
            <a href="<?php echo $this->url("admin/settings/box/fit/{$box['box_id']}"); ?>">
                <?php echo $this->lower($this->text('Fit products')); ?>
            </a>
            <?php } ?>
          </ul>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?php if (!empty($_pager)) { ?>
<?php echo $_pager; ?>
<?php } ?>
</form>
<?php } else { ?>
    <?php echo $this->text('There are no items yet'); ?>
<?php } ?>


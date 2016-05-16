<?php if ($this->_var['pager']['record_count'] > 0): ?>
<div class="page-area">
    <span onclick="<?php echo $this->_var['pager']['page_prev']; ?>" <?php if (! $this->_var['pager']['page_prev']): ?> class="disabled" <?php endif; ?>>
          <label class="pre-page"></label>
    </span>
    <?php echo $this->_var['pager']['page']; ?>/<?php echo $this->_var['pager']['page_count']; ?>
    <span onclick="<?php echo $this->_var['pager']['page_next']; ?>" <?php if (! $this->_var['pager']['page_next']): ?> class="disabled" <?php endif; ?>>
          <label class="next-page"></label>
    </span>
</div>
<?php endif; ?>


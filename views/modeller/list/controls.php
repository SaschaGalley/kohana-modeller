<div class="container controls">
    <div class="row-fluid">
        <a href="<?php echo BASE_URL.$route; ?>add<?php echo URL::query($query); ?>" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add</a>
        <a href="<?php echo BASE_URL.$route; ?>export" class="btn"><i class="icon-download-alt"></i> Export</a>
        <a href="javascript: window.print();" class="btn"><i class="icon-print"></i> Print</a>
    </div>
</div>
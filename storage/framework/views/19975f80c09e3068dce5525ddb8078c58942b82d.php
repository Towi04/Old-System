<script type="text/javascript">
    $(document).ready(function() {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 5000
        };


        <?php if(Session::has('message')): ?>
            toastr.success('Éxito', '<?php echo e(Session::get('message')); ?>');
        <?php endif; ?>

        <?php if(Session::has('error')): ?>
            toastr.error('Atención', '<?php echo e(Session::get('error')); ?>');
        <?php endif; ?>

        <?php if($errors->any()): ?>
            toastr.error('Atención', 'Corrige los siguientes errores:  <?php echo e(Session::get('error')); ?> <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>');
        <?php endif; ?>
    });
</script>

<div aria-hidden="true" aria-labelledby="modalCargando" class="modal fade" id="modalCargando" role="dialog" tabindex="-1" data-keyboard="false" data-backdrop="false">
    <div class="modal-dialog modal-sm " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
           <center><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> <br>Se esta procesando la información. Por favor espere</center>
          </h5>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    var wait = $('#modalCargando');
</script>
<?php /**PATH /Users/aldo/Sites/cncm/resources/views/partials/messages.blade.php ENDPATH**/ ?>
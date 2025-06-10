

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">Mes réservations</h1>
            
            <?php if(count($reservations) > 0): ?>
                <p class="text-muted">Voici la liste de vos réservations en cours.</p>
            <?php else: ?>
                <div class="alert alert-info">
                    Vous n'avez aucune réservation active pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if(count($reservations) > 0): ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Résumé</h5>
                        <span class="badge bg-primary fs-6">Total : <?php echo e(number_format($totalPrice, 2, ',', ' ')); ?> €</span>
                    </div>
                </div>
            </div>
        </div>

        <?php $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">
                        Réservation #<?php echo e($reservation['id']); ?>

                        <span class="text-muted fs-6 ms-2">
                            <?php echo e($reservation['date']); ?> à <?php echo e($reservation['time']); ?>

                        </span>
                    </h5>
                    <span class="badge <?php echo e($reservation['status'] === 'Payée' ? 'bg-success' : 'bg-warning text-dark'); ?>">
                        <?php echo e($reservation['status']); ?>

                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Formation</th>
                                    <th>Professeur</th>
                                    <th class="text-end pe-3">Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $reservation['trainings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="ps-3"><?php echo e($training['title']); ?></td>
                                        <td><?php echo e($training['professor_name']); ?></td>
                                        <td class="text-end pe-3"><?php echo e(number_format($training['price'], 2, ',', ' ')); ?> €</td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="ps-3 fw-bold" colspan="2">Total de la réservation</td>
                                    <td class="text-end pe-3 fw-bold"><?php echo e(number_format($reservation['total'], 2, ',', ' ')); ?> €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end py-3">
                    <?php if($reservation['status'] !== 'Payée'): ?>
                        <button 
                            class="btn btn-danger me-2" 
                            onclick="cancelReservation(<?php echo e($reservation['id']); ?>)"
                        >
                            Annuler la réservation
                        </button>
                        <a href="#" class="btn btn-primary">Procéder au paiement</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function cancelReservation(reservationId) {
        if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            fetch('/api/reservations/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    reservation_id: reservationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Erreur lors de l\'annulation de la réservation');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'annulation de la réservation');
            });
        }
    }
</script>
<?php $__env->stopSection(); ?>

<style>
    /* Styles pour la page des réservations */
.card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.table th, .table td {
    padding: 15px;
    vertical-align: middle;
}

.badge {
    padding: 8px 12px;
    border-radius: 6px;
    font-weight: 500;
}

.btn {
    padding: 8px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color:  #2B6ED4;
    border-color:  #2B6ED4;
}

.btn-primary:hover {
    background-color: #215bb8;
    border-color: #215bb8;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}
</style>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hibah\Downloads\P_Plateforme_ELS\P_Plateforme_ELS\resources\views/admin/apps/reservations/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title'); ?> Connexion  <?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<style>
    .auth-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #e8eaf6 0%, #f3e5f5 50%, #ede7f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .auth-wrapper {
        display: flex;
        width: 100%;
        max-width: 1200px;
        min-height: 80vh;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 25px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(15px);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* .left-panel {
        flex: 1;
        background: linear-gradient(135deg, #2d3355 0%, #673ab7 50%, #3f51b5 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        color: white;
        padding: 3rem;
        overflow: hidden;
    } */
	 .left-panel {
    flex: 1;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    color: white;
    padding: 3rem;
    overflow: hidden;
}

    .right-panel {
        flex: 1;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #fafafa;
    }

    /* Animation séquentielle avec des icônes */
    .sequence-animation {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
    }

    .animation-step {
        position: absolute;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        opacity: 0;
        transform: scale(0);
        animation: sequenceStep 8s infinite;
    }

    .step-1 {
        top: 30%;
        left: 20%;
        animation-delay: 0s;
    }

    .step-2 {
        top: 30%;
        right: 20%;
        animation-delay: 1.5s;
    }

    .step-3 {
        bottom: 30%;
        left: 50%;
        transform: translate(-50%, 0) scale(0);
        animation-delay: 3s;
    }

    /* Animation de connexion entre les étapes */
    .connection-line {
        position: absolute;
        height: 3px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.7), rgba(255,255,255,0.3));
        opacity: 0;
    }

    .line-1 {
        top: calc(30% + 40px);
        left: calc(20% + 80px);
        width: calc(60% - 160px);
        animation: drawLine 8s infinite;
        animation-delay: 0.75s;
    }

    .line-2 {
        bottom: calc(30% + 40px);
        right: calc(50% - 40px);
        width: calc(30% - 40px);
        animation: drawLine 8s infinite;
        animation-delay: 2.25s;
    }

    /* Particules flottantes */
    .floating-particle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        animation: floatParticle 12s infinite linear;
    }

    .particle-1 { top: 10%; left: 10%; animation-delay: 0s; }
    .particle-2 { top: 20%; left: 80%; animation-delay: 2s; }
    .particle-3 { top: 80%; left: 15%; animation-delay: 4s; }
    .particle-4 { top: 85%; left: 75%; animation-delay: 6s; }
    .particle-5 { top: 60%; left: 90%; animation-delay: 8s; }

    .welcome-text {
        text-align: center;
        z-index: 10;
        position: relative;
    }

    .welcome-text h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        opacity: 0;
        animation: fadeInUp 1s 0.5s forwards;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .welcome-text p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2rem;
        opacity: 0;
        animation: fadeInUp 1s 1s forwards;
    }

    .login-card {
        background: none !important;
        border: none;
        box-shadow: none;
        padding: 0;
    }

    .form-title {
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-title h4 {
        color: #2c3e50;
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-title h6 {
        color: #7f8c8d;
        font-weight: 400;
        margin-bottom: 0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        color: #34495e;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .input-group {
        position: relative;
    }

    .input-group-text {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border: 1px solid #2B6ED4;
        border-right: none;
        color: #2B6ED4;
        font-size: 1.1rem;
        border-radius: 12px 0 0 12px;
    }

    .form-control {
        border: 1px solid #2B6ED4;
        border-radius: 0 12px 12px 0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #ffffff;
        border-left: none;
    }

    .form-control:focus {
        border-color: #2B6ED4;
        box-shadow: 0 0 0 0.2rem rgba(93, 173, 226, 0.25);
        transform: translateY(-2px);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2B6ED4 0%, #3498db 100%);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        width: 100%;
        color: white;
    }



    .login-social-title {
        text-align: center;
        margin: 2rem 0 1rem;
        position: relative;
    }

    .login-social-title::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
        z-index: 1;
    }

    .login-social-title h5 {
        background: #fafafa;
        padding: 0 1rem;
        color: #7f8c8d;
        position: relative;
        z-index: 2;
        margin: 0;
        font-size: 0.9rem;
    }

    .login-social {
        display: flex;
        justify-content: center;
        gap: 1rem;
        list-style: none;
        padding: 0;
        margin-bottom: 2rem;
    }

    .login-social li a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        color: #2B6ED4;
        transition: all 0.3s ease;
        border: 1px solid #2B6ED4;
    }

    .login-social li:nth-child(1) a:hover { background: linear-gradient(135deg, #0077b5, #005582); color: white; }
    .login-social li:nth-child(2) a:hover { background: linear-gradient(135deg, #1da1f2, #0d8bd9); color: white; }
    .login-social li:nth-child(3) a:hover { background: linear-gradient(135deg, #1877f2, #166fe5); color: white; }
    .login-social li:nth-child(4) a:hover { background: linear-gradient(135deg, #e4405f, #d93649); color: white; }

    .login-social li a:hover {
        transform: translateY(-3px) scale(1.1);
    }

    .checkbox {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkbox input[type="checkbox"] {
        accent-color: #2B6ED4;
    }

    .checkbox label {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    .link {
        color: #2B6ED4;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .link:hover {
        text-decoration: underline;
    }

    .alert {
        border-radius: 12px;
        margin-bottom: 2rem;
        border: none;
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
    }

    .show-hide {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #2B6ED4;
        z-index: 5;
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .is-invalid ~ .invalid-feedback,
    .laravel-error {
        display: block;
    }

    /* Positionnement du lien "Mot de passe oublié" à droite */
    .forgot-password-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1.5rem;
    }

    @keyframes  sequenceStep {
        0%, 100% { opacity: 0; transform: scale(0); }
        12.5%, 62.5% { opacity: 1; transform: scale(1); }
        75% { opacity: 0; transform: scale(0); }
    }

    @keyframes  drawLine {
        0%, 100% { opacity: 0; transform: scaleX(0); }
        12.5%, 62.5% { opacity: 1; transform: scaleX(1); }
        75% { opacity: 0; transform: scaleX(0); }
    }

    @keyframes  floatParticle {
        0% { transform: translateY(0px) rotate(0deg); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
    }

    @keyframes  fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .auth-wrapper {
            flex-direction: column;
            margin: 1rem;
            min-height: auto;
        }

        .left-panel {
            min-height: 250px;
            padding: 2rem;
        }

        .right-panel {
            padding: 2rem;
        }

        .welcome-text h1 {
            font-size: 2rem;
        }

        .animation-step {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .checkbox {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .forgot-password-wrapper {
            justify-content: flex-start;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success mb-4 shadow-sm">
        <div class="d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <?php echo e(session('success')); ?>

        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Panel with Sequential Animation -->
        <div class="left-panel">
            <!-- Animation séquentielle -->
            <div class="sequence-animation">
                <!-- Étapes de l'animation -->
                <div class="animation-step step-1">👤</div>
                <div class="animation-step step-2">🔒</div>
                <div class="animation-step step-3">✨</div>

                <!-- Lignes de connexion -->
                <div class="connection-line line-1"></div>
                <div class="connection-line line-2"></div>
            </div>

            <!-- Particules flottantes -->
            <div class="floating-particle particle-1"></div>
            <div class="floating-particle particle-2"></div>
            <div class="floating-particle particle-3"></div>
            <div class="floating-particle particle-4"></div>
            <div class="floating-particle particle-5"></div>

            <div class="welcome-text">
                <h1>Bon retour !</h1>
                <p>Connectez-vous à votre compte pour accéder à toutes vos fonctionnalités</p>
            </div>
        </div>

        <!-- Right Panel with Form -->
        <div class="right-panel">
            <div class="login-card">
                <form class="theme-form login-form needs-validation" method="POST" action="<?php echo e(route('login')); ?>" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="form-title">
                        <h4>Connexion</h4>
                        <h6>Bienvenue ! Connectez-vous à votre compte.</h6>
                    </div>

                    <div class="form-group">
                        <label>Adresse Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-email"></i></span>
                            <input class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="email" name="email" required placeholder="exemple@gmail.com" value="<?php echo e(old('email')); ?>" />
                            <div class="invalid-feedback js-error">Veuillez entrer votre email.</div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="icon-lock"></i></span>
                            <input class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="password" name="password" required placeholder="*********" id="passwordField" value="<?php echo e(old('password')); ?>" />
                            <div class="show-hide" onclick="togglePassword()">
                                <i class="fa fa-eye" id="eyeIcon"></i>
                            </div>
                            <div class="invalid-feedback js-error">Veuillez entrer votre mot de passe.</div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>



                    <div class="forgot-password-wrapper">
                        <a class="link" href="<?php echo e(route('forgot.password')); ?>">Mot de passe oublié ?</a>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit">Se connecter</button>
                    </div>



                    <p style="text-align: center; color: #7f8c8d;">
                        Vous n'avez pas encore de compte ?
                        <a class="ms-2" href="<?php echo e(route('sign-up')); ?>" style="color: #2B6ED4; text-decoration: none; font-weight: 500;">Créer un compte</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/form-validation/form_validation2.js')); ?>"></script>
<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.authentication.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/auth/login.blade.php ENDPATH**/ ?>
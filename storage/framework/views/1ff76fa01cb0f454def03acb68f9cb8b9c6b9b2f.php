<?php $__env->startSection('title'); ?> S'inscrire <?php echo e($title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/sweetalert2.css')); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            top: 20%;
            left: 20%;
            animation-delay: 0s;
        }

        .step-2 {
            top: 20%;
            right: 20%;
            animation-delay: 1s;
        }

        .step-3 {
            bottom: 20%;
            right: 20%;
            animation-delay: 2s;
        }

        .step-4 {
            bottom: 20%;
            left: 20%;
            animation-delay: 3s;
        }

        .step-5 {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            animation-delay: 4s;
        }

        /* Animation de connexion entre les étapes */
        .connection-line {
            position: absolute;
            height: 3px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.7), rgba(255,255,255,0.3));
            opacity: 0;
        }

        .line-1 {
            top: calc(20% + 40px);
            left: calc(20% + 80px);
            width: calc(60% - 160px);
            animation: drawLine 8s infinite;
            animation-delay: 0.5s;
        }

        .line-2 {
            top: calc(20% + 80px);
            right: calc(20% + 40px);
            height: calc(60% - 160px);
            width: 3px;
            animation: drawLine 8s infinite;
            animation-delay: 1.5s;
        }

        .line-3 {
            bottom: calc(20% + 40px);
            right: calc(20% + 80px);
            width: calc(60% - 160px);
            animation: drawLine 8s infinite;
            animation-delay: 2.5s;
        }

        .line-4 {
            bottom: calc(20% + 80px);
            left: calc(20% + 40px);
            height: calc(60% - 160px);
            width: 3px;
            animation: drawLine 8s infinite;
            animation-delay: 3.5s;
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
        .particle-2 { top: 30%; left: 80%; animation-delay: 2s; }
        .particle-3 { top: 70%; left: 15%; animation-delay: 4s; }
        .particle-4 { top: 85%; left: 75%; animation-delay: 6s; }
        .particle-5 { top: 50%; left: 90%; animation-delay: 8s; }

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
        }

        .form-control {
            border: 1px solid #2B6ED4;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .form-control:focus {
            border-color: #2B6ED4;
            box-shadow: 0 0 0 0.2rem rgba(93, 173, 226, 0.25);
            transform: translateY(-2px);
        }

        .small-group {
            display: flex;
            gap: 1rem;
        }

        .small-group .input-group {
            flex: 1;
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
            align-items: flex-start;
            gap: 0.5rem;
        }

        .checkbox input[type="checkbox"] {
            margin-top: 0.25rem;
            accent-color: #2B6ED4;
        }

        .checkbox label {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .checkbox label span {
            color: #2B6ED4;
            text-decoration: underline;
        }

        .alert-danger {
            border-radius: 12px;
            margin-bottom: 2rem;
            border: none;
            background: linear-gradient(135deg, #ffebee, #fce4ec);
            color: #c62828;
        }

        .show-hide {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #2B6ED4;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            color: #dc3545;
        }

        .is-invalid {
            border-color: #dc3545;
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

            .small-group {
                flex-direction: column;
                gap: 1rem;
            }

            .animation-step {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<section>
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="auth-container">
                    <div class="auth-wrapper">
                        <!-- Left Panel with Sequential Animation -->
                        <div class="left-panel">
                            <!-- Animation séquentielle -->
                            <div class="sequence-animation">
                                <!-- Étapes de l'animation -->
                                <div class="animation-step step-1">👤</div>
                                <div class="animation-step step-2">📧</div>
                                <div class="animation-step step-3">🔒</div>
                                <div class="animation-step step-4">📱</div>

                                <!-- Lignes de connexion -->
                                <div class="connection-line line-1"></div>
                                <div class="connection-line line-2"></div>
                                <div class="connection-line line-3"></div>
                                <div class="connection-line line-4"></div>
                            </div>

                            <!-- Particules flottantes -->
                            <div class="floating-particle particle-1"></div>
                            <div class="floating-particle particle-2"></div>
                            <div class="floating-particle particle-3"></div>
                            <div class="floating-particle particle-4"></div>
                            <div class="floating-particle particle-5"></div>

                            <div class="welcome-text">
                                <h1>Rejoignez-nous !</h1>
                                <p>Créez votre compte en quelques étapes simples et découvrez toutes nos fonctionnalités</p>
                            </div>
                        </div>

                        <!-- Right Panel with Form -->
                        <div class="right-panel">
                            <div class="login-card">
                                <form class="theme-form login-form needs-validation" method="POST" action="<?php echo e(route('register')); ?>" novalidate>
                                    <?php echo csrf_field(); ?>
                                    <div class="form-title">
                                        <h4>Créer un compte</h4>
                                        <h6>Entrez vos informations personnelles pour créer un compte</h6>
                                    </div>

                                    <div class="form-group">
                                        <label>Votre Nom Complet</label>
                                        <div class="small-group">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                <input class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" type="text" required placeholder="Prénom" value="<?php echo e(old('name')); ?>" />
                                                <div class="invalid-feedback js-error">Veuillez entrer un prénom.</div>
                                                <?php $__errorArgs = ['name'];
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
                                            <div class="input-group">

                                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                <input class="form-control <?php $__errorArgs = ['lastname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="lastname" type="text" required placeholder="Nom de famille" value="<?php echo e(old('lastname')); ?>" />
                                                <div class="invalid-feedback js-error">Veuillez entrer un nom.</div>
                                                <?php $__errorArgs = ['lastname'];
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
                                    </div>

                                    <div class="form-group">
                                        <label>Adresse Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" type="email" required placeholder="exemple@gmail.com" value="<?php echo e(old('email')); ?>" />
                                            <div class="invalid-feedback js-error">Veuillez entrer une adresse email valide.</div>
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
                                        <label>Numéro de Téléphone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                            <input class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="text" name="phone" required placeholder="+216 12 345 678" value="<?php echo e(old('phone')); ?>" />
                                            <div class="invalid-feedback js-error">
                                                Veuillez entrer un numéro de téléphone valide.
                                            </div>
                                            <?php $__errorArgs = ['phone'];
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
                                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                            <input class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="password" name="password" required placeholder="***" id="passwordField" value="<?php echo e(old('password')); ?>" />
                                            <div class="show-hide" onclick="togglePassword()">
                                                <i class="fa fa-eye" id="eyeIcon"></i>
                                            </div>
                                            <div class="invalid-feedback js-error">Le mot de passe doit contenir au moins 8 caractères.</div>
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

                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input id="checkbox1" type="checkbox" name="privacy_policy" value="1" required <?php echo e(old('privacy_policy') ? 'checked' : ''); ?> />
                                            <label class="text-muted" for="checkbox1">J'accepte la <span>Politique de confidentialité</span></label>
                                            <div class="invalid-feedback js-error">Veuillez accepter la Politique de confidentialité.</div>
                                            <?php $__errorArgs = ['privacy_policy'];
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
                                        <button class="btn btn-primary btn-block" type="submit">Créer un compte</button>
                                    </div>



                                    <p style="text-align: center; color: #7f8c8d;">
                                        Vous avez déjà un compte ?
                                        <a class="ms-2" href="<?php echo e(route('login')); ?>" style="color: #2B6ED4; text-decoration: none; font-weight: 500;">Se connecter</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.authentication.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\apprendre laravel\Centre_Formation-main\resources\views/admin/authentication/sign-up.blade.php ENDPATH**/ ?>
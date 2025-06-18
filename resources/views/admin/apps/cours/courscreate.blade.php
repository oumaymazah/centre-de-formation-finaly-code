 @extends('layouts.admin.master')

@section('title') Ajouter un Cours @endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dropzone.css') }}">
    <link href="{{ asset('assets/css/MonCss/custom-style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/css/MonCss/SweatAlert2.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/MonCss/custom-calendar.css') }}">

@endpush

@section('content')
@php
    $selectedFormationId = request()->query('training_id', old('training_id'));
@endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Nouveau cours</h5>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Alerte d'information sur le calcul automatique de la durée -->
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Note:</strong> La durée du cours sera calculée automatiquement en fonction des chapitres que vous ajouterez ultérieurement.
                        </div>

                        <div class="form theme-form">
                            <form action="{{ route('coursstore') }}" method="POST" class="needs-validation" novalidate>
                                @csrf

                                <!-- Titre -->
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Titre <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                            <input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="{{ old('title') }}" required />
                                            <div class="invalid-feedback">Veuillez entrer un titre valide.</div>

                                        </div>
                                        {{-- <div class="invalid-feedback">Veuillez entrer un titre valide.</div> --}}
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Description <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="input-group" style="flex-wrap: nowrap;">
                                            <div class="input-group-text d-flex align-items-stretch" style="height: auto;">
                                                <i class="fa fa-align-left align-self-center"></i>
                                            </div>
                                            <textarea class="form-control" id="description" name="description" placeholder="Description" >{{ old('description') }}</textarea>
                                        </div>
                                        <div class="invalid-feedback">Veuillez entrer une description valide.</div>
                                    </div>
                                </div>


<div class="mb-3 row">
    <label class="col-sm-2 col-form-label">Périodes <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <div class="row">
            <!-- Date de début -->
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <input class="form-control datepicker"
                           type="text"
                           id="start_date"
                           name="start_date"
                           placeholder="Sélectionner une date"
                           value="{{ old('start_date') }}"
                           readonly
                           required />
                                           <div class="invalid-feedback">Veuillez sélectionner une date de début valide.</div>

                </div>
                <small class="text-muted">Date début</small>
            </div>
            <!-- Date de fin -->
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <input class="form-control datepicker"
                           type="text"
                           id="end_date"
                           name="end_date"
                           placeholder="Sélectionner une date"
                           value="{{ old('end_date') }}"
                           readonly
                           required />
                                           <div class="invalid-feedback">Veuillez sélectionner une date de fin valide.</div>

                </div>
                <small class="text-muted">Date fin</small>
            </div>
        </div>
    </div>
</div>

                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Formation <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-auto">
                                                <span class="input-group-text"><i class="fa fa-book"></i></span>
                                            </div>
                                            <div class="col">
                                                @php
                                                    $fromUrl = session('from_url') ?? request()->has('from_url');
                                                @endphp

                                                @if($selectedFormationId && ($fromUrl || request()->has('training_id')))
                                                    @php
                                                        $selectedFormation = $formations->firstWhere('id', $selectedFormationId);
                                                    @endphp
                                                        <!-- Mode URL : Affichage verrouillé -->

                                                    <input type="text" class="form-control bg-light selected-course-bg" value="{{ $selectedFormation ? $selectedFormation->title : '' }}" readonly />
                                                    <input type="hidden" name="training_id" value="{{ $selectedFormationId }}">
                                                    <input type="hidden" name="from_url" value="true">
                                                @elseif($selectedFormationId)
                                                    <!-- Mode normal : Sélection libre -->

                                                    <select class="form-select select2-formation" name="training_id" required>
                                                        <option value="" disabled>Choisir une formation</option>
                                                        @foreach($formations as $formation)
                                                            <option value="{{ $formation->id }}" {{ $selectedFormationId == $formation->id ? 'selected' : '' }} class="{{ $selectedFormationId == $formation->id ? 'selected-course-bg' : '' }}">
                                                                {{ $formation->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select class="form-select select2-formation" name="training_id" required>
                                                        <option value="" disabled selected>Choisir une formation</option>
                                                        @foreach($formations as $formation)
                                                            <option value="{{ $formation->id }}">
                                                                {{ $formation->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                <div class="invalid-feedback">Veuillez sélectionner une formation valide.</div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Boutons de soumission -->
                                <div class="row">
                                    <div class="col">
                                        <div class="text-end mt-4">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-save"></i> Ajouter
                                            </button>
                                            <button class="btn btn-danger" type="button" onclick="window.location.href='{{ route('cours') }}'">
                                                <i class="fa fa-times"></i> Annuler
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone-script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/MonJs/select2-init/single-select.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/form-validation/form-validation.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/MonJs/description/description.js') }}"></script>
    <script src="https://cdn.tiny.cloud/1/kekmlqdijg5r326hw82c8zalt4qp1hl0ui3v3tim9vh1xpzv/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js"></script>
    <script src="{{ asset('assets/js/MonJs/calendar/custom-calendar.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Obtenir les paramètres d'URL
    const urlParams = new URLSearchParams(window.location.search);
    const trainingIdFromUrl = urlParams.get('training_id');
    const fromUrl = urlParams.get('from_url');

    // Gestion de la notification après l'ajout du cours
    let coursId = "{{ session('cours_id') }}";
    let fromUrlSession = "{{ session('from_url') }}";

    if (coursId) {
        Swal.fire({
            title: "Cours ajouté avec succès !",
            text: "Voulez-vous ajouter un chapitre à ce cours ? (La durée du cours sera calculée automatiquement)",
            icon: "success",
            showCancelButton: true,
            confirmButtonText: "Oui, ajouter un chapitre",
            cancelButtonText: "Non, revenir à la liste",
            showCloseButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            customClass: {
                confirmButton: 'custom-confirm-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Si le cours vient d'une URL spécifique, conserver le paramètre from_url
                if (fromUrlSession === '1' || fromUrl === 'true') {
                    window.location.href = "{{ route('chapitrecreate') }}?cours_id=" + coursId + "&from_url=true";
                } else {
                    window.location.href = "{{ route('chapitrecreate') }}?cours_id=" + coursId;
                }
            } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                // Si le cours vient d'une URL spécifique, conserver le paramètre from_url
                if (fromUrlSession === '1' || fromUrl === 'true') {
                    window.location.href = "{{ route('cours') }}?from_url=true";
                } else {
                    window.location.href = "{{ route('cours') }}";
                }
            }
        });
    }

    // Appliquer le fond bleu à l'option sélectionnée dans le dropdown de Select2
    const coursSelect = document.querySelector('.select2-formation');
    if (coursSelect) {
        // Appliquer le fond bleu à l'option sélectionnée au chargement de la page
        const selectedOption = coursSelect.options[coursSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            selectedOption.classList.add('selected-course-bg');
        }

        // Appliquer le fond bleu à l'option sélectionnée lorsqu'elle change
        coursSelect.addEventListener('change', function() {
            // Supprimer la classe de l'ancienne option sélectionnée
            const previousSelectedOption = coursSelect.querySelector('.selected-course-bg');
            if (previousSelectedOption) {
                previousSelectedOption.classList.remove('selected-course-bg');
            }

            // Ajouter la classe à la nouvelle option sélectionnée
            const newSelectedOption = coursSelect.options[coursSelect.selectedIndex];
            if (newSelectedOption && newSelectedOption.value) {
                newSelectedOption.classList.add('selected-course-bg');
            }
        });
    }
});

</script>
{{--
<script>
    // Initialisation des datepickers avec validation
$(document).ready(function() {
    // Configuration des datepickers
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'fr',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date(),
        orientation: 'bottom auto'
    }).on('changeDate', function(e) {
        // Déclencher la validation quand une date est sélectionnée
        const input = e.target;

        // Forcer la mise à jour de la validation
        setTimeout(function() {
            // Supprimer les classes de validation existantes
            input.classList.remove('is-invalid', 'is-valid');

            // Ajouter la classe de succès si une valeur existe
            if (input.value && input.value.trim() !== '') {
                input.classList.add('is-valid');
            } else {
                input.classList.add('is-invalid');
            }

            // Validation spéciale pour les dates de début et de fin
            validateDateRange();
        }, 50);
    }).on('hide', function(e) {
        // Validation quand le datepicker se ferme
        const input = e.target;
        setTimeout(function() {
            if (input.value && input.value.trim() !== '') {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
            validateDateRange();
        }, 50);
    });

    // Fonction pour valider la plage de dates
    function validateDateRange() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
            const startDate = parseDate(startDateInput.value);
            const endDate = parseDate(endDateInput.value);

            if (startDate && endDate && startDate >= endDate) {
                endDateInput.classList.remove('is-valid');
                endDateInput.classList.add('is-invalid');

                // Mettre à jour le message d'erreur
                const errorMsg = endDateInput.closest('.col-md-6').querySelector('.invalid-feedback');
                if (errorMsg) {
                    errorMsg.textContent = 'La date de fin doit être postérieure à la date de début.';
                }
            } else if (startDate && endDate) {
                // Si les dates sont valides, s'assurer que les deux champs sont marqués comme valides
                if (endDateInput.classList.contains('is-invalid')) {
                    endDateInput.classList.remove('is-invalid');
                    endDateInput.classList.add('is-valid');

                    // Restaurer le message d'erreur original
                    const errorMsg = endDateInput.closest('.col-md-6').querySelector('.invalid-feedback');
                    if (errorMsg) {
                        errorMsg.textContent = 'Veuillez sélectionner une date de fin valide.';
                    }
                }
            }
        }
    }

    // Fonction utilitaire pour parser les dates au format dd/mm/yyyy
    function parseDate(dateString) {
        if (!dateString) return null;
        const parts = dateString.split('/');
        if (parts.length !== 3) return null;

        const day = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1; // Les mois sont indexés à partir de 0
        const year = parseInt(parts[2], 10);

        return new Date(year, month, day);
    }
});

// Script pour la gestion de la validation en temps réel (sans jQuery)
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de validation pour les champs de date
    function validateDateInput(input) {
        const value = input.value ? input.value.trim() : '';

        if (value && value !== '') {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            return false;
        }
    }

    // Écouter les changements sur les champs de date
    const dateInputs = document.querySelectorAll('.datepicker');

    dateInputs.forEach(function(input) {
        // Créer un observer pour surveiller les changements de l'attribut value
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    validateDateInput(input);
                }
            });
        });

        // Observer les changements d'attributs
        observer.observe(input, {
            attributes: true,
            attributeFilter: ['value']
        });

        // Vérification périodique comme fallback
        let lastValue = input.value;
        const checkValue = setInterval(function() {
            if (input.value !== lastValue) {
                lastValue = input.value;
                validateDateInput(input);
            }
        }, 200);

        // Nettoyer l'interval quand l'élément est supprimé
        input.addEventListener('DOMNodeRemoved', function() {
            clearInterval(checkValue);
        });

        // Écouter l'événement focus pour une validation immédiate
        input.addEventListener('focus', function() {
            // Petite temporisation pour laisser le datepicker s'initialiser
            setTimeout(function() {
                validateDateInput(input);
            }, 100);
        });

        // Écouter l'événement blur
        input.addEventListener('blur', function() {
            setTimeout(function() {
                validateDateInput(input);
            }, 100);
        });
    });
});
</script> --}}
@endpush

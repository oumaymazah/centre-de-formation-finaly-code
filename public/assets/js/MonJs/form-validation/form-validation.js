
// // (function () {
// //     'use strict';
// //     window.addEventListener('click', function () {
// //         var forms = document.querySelectorAll('.needs-validation');
// //         console.log("test");


// //         forms.forEach(function (form) {
// //             // Écoute en temps réel pour chaque champ
// //             var inputFields = form.querySelectorAll('input, textarea, select');

// //             inputFields.forEach(function (input) {
// //                 const validateInput = function() {
// //                     if (input.checkValidity()) {
// //                         input.classList.remove('is-invalid');
// //                         input.classList.add('is-valid');
// //                     } else {
// //                         input.classList.remove('is-valid');
// //                         input.classList.add('is-invalid');
// //                     }
// //                 };

// //                 // Pour les inputs et textarea
// //                 input.addEventListener('input', validateInput);

// //                 // Pour les select
// //                 if (input.tagName.toLowerCase() === 'select') {
// //                     input.addEventListener('change', validateInput);
// //                 }

// //                 // Validation initiale
// //                 validateInput();
// //             });

// //             // Gestion de la soumission du formulaire
// //             form.addEventListener('submit', function (event) {
// //                 event.preventDefault(); // Empêcher la soumission par défaut

// //                 var isValid = true;
// //                 inputFields.forEach(function (input) {
// //                     if (!input.checkValidity()) {
// //                         isValid = false;
// //                         input.classList.add('is-invalid');
// //                         input.classList.remove('is-valid');
// //                     }
// //                 });

// //                 form.classList.add('was-validated');

// //                 if (isValid) {
// //                     // Si le formulaire est valide, le soumettre
// //                     form.submit();
// //                 }
// //             });
// //         });
// //     });
// // })();




// // (function () {
// //     'use strict';
// //     window.addEventListener('load', function () {  // Utilisez 'load' au lieu de 'click'
// //         var forms = document.querySelectorAll('.needs-validation');
// //         console.log("test");

// //         forms.forEach(function (form) {
// //             // Écoute en temps réel pour chaque champ
// //             var inputFields = form.querySelectorAll('input, textarea, select');

// //             inputFields.forEach(function (input) {
// //                 const validateInput = function() {
// //                     if (input.checkValidity()) {
// //                         input.classList.remove('is-invalid');
// //                         input.classList.add('is-valid');
// //                     } else {
// //                         input.classList.remove('is-valid');
// //                         input.classList.add('is-invalid');
// //                     }
// //                 };

// //                 // Pour les inputs et textarea
// //                 input.addEventListener('input', validateInput);

// //                 // Pour les select
// //                 if (input.tagName.toLowerCase() === 'select') {
// //                     input.addEventListener('change', validateInput);
// //                 }

// //                 // Supprimez l'appel initial : ne pas appeler validateInput() ici
// //                 // validateInput();
// //             });

// //             // Gestion de la soumission du formulaire
// //             form.addEventListener('submit', function (event) {
// //                 event.preventDefault(); // Empêcher la soumission par défaut

// //                 var isValid = true;
// //                 inputFields.forEach(function (input) {
// //                     if (!input.checkValidity()) {
// //                         isValid = false;
// //                         input.classList.add('is-invalid');
// //                         input.classList.remove('is-valid');
// //                     }
// //                 });

// //                 form.classList.add('was-validated');
// //                 if (isValid) {
// //                     // Si le formulaire est valide, le soumettre
// //                     form.submit();
// //                 }
// //             });
// //         });
// //     });
// // })();
// // (function () {
// //     'use strict';
// //     window.addEventListener('load', function () {
// //         var forms = document.querySelectorAll('.needs-validation');
// //         console.log("Form validation initialized");

// //         forms.forEach(function (form) {
// //             var inputFields = form.querySelectorAll('input, textarea, select');

// //             // Fonction de validation personnalisée pour les champs de date
// //             const validateDateField = function(dateInput) {
// //                 const value = dateInput.value ? dateInput.value.trim() : '';

// //                 // Vérifier si le champ a une valeur (peu importe le format)
// //                 if (value === '' || value === null || value === undefined) {
// //                     dateInput.classList.remove('is-valid');
// //                     dateInput.classList.add('is-invalid');
// //                     return false;
// //                 } else {
// //                     // Si le champ a une valeur, le considérer comme valide
// //                     dateInput.classList.remove('is-invalid');
// //                     dateInput.classList.add('is-valid');
// //                     return true;
// //                 }
// //             };

// //             // Fonction de validation générale
// //             const validateInput = function(input) {
// //                 // Traitement spécial pour les champs de date avec datepicker
// //                 if (input.classList.contains('datepicker')) {
// //                     return validateDateField(input);
// //                 }

// //                 // Validation normale pour les autres champs
// //                 if (input.checkValidity()) {
// //                     input.classList.remove('is-invalid');
// //                     input.classList.add('is-valid');
// //                     return true;
// //                 } else {
// //                     input.classList.remove('is-valid');
// //                     input.classList.add('is-invalid');
// //                     return false;
// //                 }
// //             };

// //             inputFields.forEach(function (input) {
// //                 // Pour les inputs normaux et textarea
// //                 if (input.tagName.toLowerCase() !== 'select') {
// //                     input.addEventListener('input', function() {
// //                         validateInput(input);
// //                     });

// //                     // Pour les champs de date, écouter aussi l'événement 'change'
// //                     if (input.classList.contains('datepicker')) {
// //                         input.addEventListener('change', function() {
// //                             validateInput(input);
// //                         });

// //                         // Écouter l'événement 'changeDate' du datepicker Bootstrap
// //                         input.addEventListener('changeDate', function() {
// //                             setTimeout(function() {
// //                                 validateInput(input);
// //                             }, 100);
// //                         });

// //                         // Écouter aussi l'événement DOMSubtreeModified ou MutationObserver pour les changements de valeur
// //                         const observer = new MutationObserver(function(mutations) {
// //                             mutations.forEach(function(mutation) {
// //                                 if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
// //                                     validateInput(input);
// //                                 }
// //                             });
// //                         });
// //                         observer.observe(input, {
// //                             attributes: true,
// //                             attributeFilter: ['value']
// //                         });

// //                         // Vérifier périodiquement la valeur (fallback)
// //                         let lastValue = input.value;
// //                         setInterval(function() {
// //                             if (input.value !== lastValue) {
// //                                 lastValue = input.value;
// //                                 validateInput(input);
// //                             }
// //                         }, 500);
// //                     }
// //                 }

// //                 // Pour les select
// //                 if (input.tagName.toLowerCase() === 'select') {
// //                     input.addEventListener('change', function() {
// //                         validateInput(input);
// //                     });
// //                 }
// //             });

// //             // Gestion de la soumission du formulaire
// //             form.addEventListener('submit', function (event) {
// //                 event.preventDefault();

// //                 var isValid = true;

// //                 inputFields.forEach(function (input) {
// //                     if (!validateInput(input)) {
// //                         isValid = false;
// //                     }
// //                 });

// //                 // Validation supplémentaire pour s'assurer que la date de fin est après la date de début
// //                 const startDateInput = form.querySelector('#start_date');
// //                 const endDateInput = form.querySelector('#end_date');

// //                 if (startDateInput && endDateInput) {
// //                     const startDate = new Date(startDateInput.value);
// //                     const endDate = new Date(endDateInput.value);

// //                     if (startDateInput.value && endDateInput.value && startDate >= endDate) {
// //                         endDateInput.classList.add('is-invalid');
// //                         endDateInput.classList.remove('is-valid');

// //                         // Mettre à jour le message d'erreur
// //                         const errorMsg = endDateInput.parentNode.parentNode.querySelector('.invalid-feedback');
// //                         if (errorMsg) {
// //                             errorMsg.textContent = 'La date de fin doit être postérieure à la date de début.';
// //                         }

// //                         isValid = false;
// //                     }
// //                 }

// //                 form.classList.add('was-validated');

// //                 if (isValid) {
// //                     console.log("Form is valid, submitting...");
// //                     form.submit();
// //                 } else {
// //                     console.log("Form has validation errors");

// //                     // Faire défiler vers le premier champ invalide
// //                     const firstInvalidField = form.querySelector('.is-invalid');
// //                     if (firstInvalidField) {
// //                         firstInvalidField.scrollIntoView({
// //                             behavior: 'smooth',
// //                             block: 'center'
// //                         });
// //                         firstInvalidField.focus();
// //                     }
// //                 }
// //             });
// //         });
// //     });
// // })();

// (function () {
//     'use strict';
//     window.addEventListener('load', function () {
//         var forms = document.querySelectorAll('.needs-validation');
//         console.log("Form validation initialized");

//         forms.forEach(function (form) {
//             var inputFields = form.querySelectorAll('input, textarea, select');

//             // Fonction de validation personnalisée pour les champs de date
//             const validateDateField = function(dateInput) {
//                 const value = dateInput.value ? dateInput.value.trim() : '';

//                 // Vérifier si le champ a une valeur (peu importe le format)
//                 if (value === '' || value === null || value === undefined) {
//                     dateInput.classList.remove('is-valid');
//                     dateInput.classList.add('is-invalid');
//                     return false;
//                 } else {
//                     // Si le champ a une valeur, le considérer comme valide
//                     dateInput.classList.remove('is-invalid');
//                     dateInput.classList.add('is-valid');
//                     return true;
//                 }
//             };

//             // Fonction de validation générale
//             const validateInput = function(input) {
//                 // Traitement spécial pour les champs de date avec datepicker
//                 if (input.classList.contains('datepicker')) {
//                     return validateDateField(input);
//                 }

//                 // Validation normale pour les autres champs
//                 if (input.checkValidity()) {
//                     input.classList.remove('is-invalid');
//                     input.classList.add('is-valid');
//                     return true;
//                 } else {
//                     input.classList.remove('is-valid');
//                     input.classList.add('is-invalid');
//                     return false;
//                 }
//             };

//             inputFields.forEach(function (input) {
//                 // Pour les inputs normaux et textarea
//                 if (input.tagName.toLowerCase() !== 'select') {
//                     input.addEventListener('input', function() {
//                         validateInput(input);
//                     });

//                     // Pour les champs de date, écouter aussi l'événement 'change'
//                     if (input.classList.contains('datepicker')) {
//                         input.addEventListener('change', function() {
//                             validateInput(input);
//                         });

//                         // Écouter l'événement 'changeDate' du datepicker Bootstrap
//                         input.addEventListener('changeDate', function() {
//                             setTimeout(function() {
//                                 validateInput(input);
//                             }, 100);
//                         });

//                         // Écouter aussi l'événement DOMSubtreeModified ou MutationObserver pour les changements de valeur
//                         const observer = new MutationObserver(function(mutations) {
//                             mutations.forEach(function(mutation) {
//                                 if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
//                                     validateInput(input);
//                                 }
//                             });
//                         });
//                         observer.observe(input, {
//                             attributes: true,
//                             attributeFilter: ['value']
//                         });

//                         // Vérifier périodiquement la valeur (fallback)
//                         let lastValue = input.value;
//                         setInterval(function() {
//                             if (input.value !== lastValue) {
//                                 lastValue = input.value;
//                                 validateInput(input);
//                             }
//                         }, 500);
//                     }
//                 }

//                 // Pour les select
//                 if (input.tagName.toLowerCase() === 'select') {
//                     input.addEventListener('change', function() {
//                         validateInput(input);
//                     });
//                 }
//             });

//             // Gestion de la soumission du formulaire - CORRECTION PRINCIPALE
//             form.addEventListener('submit', function (event) {
//                 console.log("Form submission intercepted");

//                 var isValid = true;

//                 // Valider tous les champs
//                 inputFields.forEach(function (input) {
//                     if (!validateInput(input)) {
//                         isValid = false;
//                     }
//                 });

//                 // Validation supplémentaire pour s'assurer que la date de fin est après la date de début
//                 const startDateInput = form.querySelector('#start_date');
//                 const endDateInput = form.querySelector('#end_date');

//                 if (startDateInput && endDateInput) {
//                     const startDate = new Date(startDateInput.value);
//                     const endDate = new Date(endDateInput.value);

//                     if (startDateInput.value && endDateInput.value && startDate >= endDate) {
//                         endDateInput.classList.add('is-invalid');
//                         endDateInput.classList.remove('is-valid');

//                         // Mettre à jour le message d'erreur
//                         const errorMsg = endDateInput.parentNode.parentNode.querySelector('.invalid-feedback');
//                         if (errorMsg) {
//                             errorMsg.textContent = 'La date de fin doit être postérieure à la date de début.';
//                         }

//                         isValid = false;
//                     }
//                 }

//                 // CORRECTION : Ne pas bloquer la soumission si le formulaire est valide
//                 if (!isValid) {
//                     // Seulement empêcher la soumission si le formulaire n'est pas valide
//                     event.preventDefault();
//                     event.stopPropagation();

//                     console.log("Form has validation errors - submission blocked");

//                     // Faire défiler vers le premier champ invalide
//                     const firstInvalidField = form.querySelector('.is-invalid');
//                     if (firstInvalidField) {
//                         firstInvalidField.scrollIntoView({
//                             behavior: 'smooth',
//                             block: 'center'
//                         });
//                         firstInvalidField.focus();
//                     }
//                 } else {
//                     console.log("Form is valid - allowing normal submission");
//                     // Ne pas empêcher la soumission - laisser le formulaire se soumettre naturellement
//                 }

//                 form.classList.add('was-validated');
//             });
//         });
//     });
// })();

(function () {
    'use strict';
    window.addEventListener('load', function () {
        var forms = document.querySelectorAll('.needs-validation');
        console.log("Form validation initialized");

        forms.forEach(function (form) {
            var inputFields = form.querySelectorAll('input, textarea, select');

            // Fonction de validation personnalisée pour les champs de date
            const validateDateField = function(dateInput) {
                const value = dateInput.value ? dateInput.value.trim() : '';

                if (value === '' || value === null || value === undefined) {
                    dateInput.classList.remove('is-valid');
                    dateInput.classList.add('is-invalid');
                    return false;
                } else {
                    dateInput.classList.remove('is-invalid');
                    dateInput.classList.add('is-valid');
                    return true;
                }
            };

            // Fonction de validation pour Select2
            const validateSelect2Field = function(selectInput) {
                const value = selectInput.value;

                if (!value || value === '' || value === null) {
                    selectInput.classList.remove('is-valid');
                    selectInput.classList.add('is-invalid');
                    return false;
                } else {
                    selectInput.classList.remove('is-invalid');
                    selectInput.classList.add('is-valid');
                    return true;
                }
            };

            // Fonction pour vérifier si un champ est requis et visible
            const isFieldRequired = function(input) {
                // Si le champ a l'attribut required
                if (input.hasAttribute('required')) {
                    // Vérifier si le champ est visible (pour les champs conditionnels comme le prix)
                    const container = input.closest('.row');
                    if (container) {
                        const computedStyle = window.getComputedStyle(container);
                        return computedStyle.display !== 'none';
                    }
                    return true;
                }
                return false;
            };

            // Fonction de validation générale
            const validateInput = function(input) {
                // Si le champ n'est pas requis ou n'est pas visible, le considérer comme valide
                if (!isFieldRequired(input)) {
                    input.classList.remove('is-invalid');
                    input.classList.remove('is-valid');
                    return true;
                }

                // Traitement spécial pour les champs Select2
                if (input.classList.contains('select2-categorie') || input.classList.contains('select2-professeur')) {
                    return validateSelect2Field(input);
                }

                // Traitement spécial pour les champs de date avec datepicker
                if (input.classList.contains('datepicker')) {
                    return validateDateField(input);
                }

                // Validation normale pour les autres champs
                if (input.checkValidity()) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    return true;
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    return false;
                }
            };

            inputFields.forEach(function (input) {
                // Pour les inputs normaux et textarea
                if (input.tagName.toLowerCase() !== 'select') {
                    input.addEventListener('input', function() {
                        validateInput(input);
                    });

                    // Pour les champs de date, écouter aussi l'événement 'change'
                    if (input.classList.contains('datepicker')) {
                        input.addEventListener('change', function() {
                            validateInput(input);
                        });

                        // Écouter l'événement 'changeDate' du datepicker Bootstrap
                        input.addEventListener('changeDate', function() {
                            setTimeout(function() {
                                validateInput(input);
                            }, 100);
                        });
                    }
                }

                // Pour les select (y compris Select2)
                if (input.tagName.toLowerCase() === 'select') {
                    input.addEventListener('change', function() {
                        validateInput(input);
                    });
                }
            });

            // Gestion de la soumission du formulaire
            form.addEventListener('submit', function (event) {
                console.log("Form submission intercepted");

                var isValid = true;
                var invalidFields = [];

                // Valider tous les champs requis et visibles
                inputFields.forEach(function (input) {
                    if (isFieldRequired(input)) {
                        if (!validateInput(input)) {
                            isValid = false;
                            invalidFields.push({
                                field: input.name || input.id,
                                value: input.value,
                                type: input.type
                            });
                        }
                    }
                });

                // Validation supplémentaire pour les dates
                const startDateInput = form.querySelector('#start_date');
                const endDateInput = form.querySelector('#end_date');

                if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
                    // Conversion des dates au format DD/MM/YYYY vers Date
                    const parseDate = function(dateStr) {
                        const parts = dateStr.split('/');
                        if (parts.length === 3) {
                            return new Date(parts[2], parts[1] - 1, parts[0]); // YYYY, MM-1, DD
                        }
                        return new Date(dateStr);
                    };

                    const startDate = parseDate(startDateInput.value);
                    const endDate = parseDate(endDateInput.value);

                    if (startDate >= endDate) {
                        endDateInput.classList.add('is-invalid');
                        endDateInput.classList.remove('is-valid');

                        const errorMsg = endDateInput.closest('.row').querySelector('.invalid-feedback');
                        if (errorMsg) {
                            errorMsg.textContent = 'La date de fin doit être postérieure à la date de début.';
                        }

                        isValid = false;
                        invalidFields.push({
                            field: 'end_date',
                            error: 'Date de fin invalide'
                        });
                    }
                }

                // Debug : afficher les champs invalides
                if (!isValid) {
                    console.log("Champs invalides détectés:", invalidFields);
                }

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();

                    console.log("Form has validation errors - submission blocked");

                    // Faire défiler vers le premier champ invalide
                    const firstInvalidField = form.querySelector('.is-invalid');
                    if (firstInvalidField) {
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalidField.focus();
                    }
                } else {
                    console.log("Form is valid - allowing normal submission");
                }

                form.classList.add('was-validated');
            });

            // Gestion du changement de type (payante/gratuite)
            const typeSelect = form.querySelector('#type');
            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    // Revalider les champs après changement de type
                    setTimeout(function() {
                        inputFields.forEach(function(input) {
                            if (isFieldRequired(input)) {
                                validateInput(input);
                            }
                        });
                    }, 100);
                });
            }
        });
    });
})();

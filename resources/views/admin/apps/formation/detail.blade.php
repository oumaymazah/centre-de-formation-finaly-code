
@extends('layouts.admin.master')

@section('title')
Détails de la Formation {{ $title }}
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/MonCss/formation/training-detail.css') }}">
@endpush

@section('content')
<!-- Assurez-vous que le CSRF token est inclus -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container-fluid training-detail-container @if(!auth()->check()) not-logged-in @endif">

    <div class="formation-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <!-- Bloc Titre -->
                <div class="title-block mb-3">
                    <h2 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chalkboard me-2 text-white"></i>
                        {{ $formation->title }}
                    </h2>
                </div>

                <!-- Bloc Informations (Formateur + Description) -->
                <div class="info-block mb-3">
                    <p class="mb-2 d-flex align-items-center">
                        <i class="fas fa-user-tie me-2 text-white"></i>
                        <strong>Formateur :</strong> {{ $formation->user->name }} {{ $formation->user->lastname }}
                    </p>
                    <p class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-white"></i>
                        <strong>Description :</strong>
                        <span>{!! $formation->description !!}</span>
                    </p>
                </div>

                <!-- Bloc Feedbacks -->
                <div class="feedback-block">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rating-stars me-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($averageRating))
                                    <i class="fa fa-star text-warning"></i>
                                @else
                                    <i class="fa fa-star-o text-muted"></i>
                                @endif
                            @endfor
                        </div>
                        <span>{{ number_format($averageRating, 1) }} ({{ $totalFeedbacks }} avis)</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="formation-stats">
                    <div class="stat-item">
                        <strong>{{ $formation->courses->count() }}</strong><br>
                        <small>Cours</small>
                    </div>
                    <div class="stat-item">
                        <strong>{{ $formation->duration ?? '0' }}</strong><br>
                        <small>Heures</small>
                    </div>
                    <div class="stat-item">
                        <strong>{{ $formation->type == 'gratuite' ? 'Gratuite' : $formation->price . ' Dt' }}</strong><br>
                        <small>Prix</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Zone de contenu principal -->
        <div class="col-lg-8">
            <div class="content-area" id="lesson-content">
                <div class="text-center py-5">
                    <i class="fa fa-book fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Sélectionnez une leçon pour commencer</h4>
                    <p class="text-muted">Choisissez une leçon dans la structure du cours à droite pour afficher son contenu ici.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar avec structure -->
        <div class="col-lg-4">
            <div class="structure-sidebar">
                <h5 class="mb-3">
                    <i class="fa fa-list-alt me-2"></i>
                    Structure du cours
                </h5>

                <!-- Courses et chapitres -->
                @foreach($courseStructure as $course)
                    <div class="course-item">
                        <div class="course-header" data-course-id="{{ $course['id'] }}">
                            <span>
                                <i class="fa fa-chevron-right toggle-icon" id="course-icon-{{ $course['id'] }}"></i>
                                {{ $course['title'] }}
                            </span>
                            <span class="badge bg-light text-dark">{{ count($course['chapters']) }}</span>
                        </div>
                        <div class="course-content" id="course-{{ $course['id'] }}" style="display: none;">
                            @foreach($course['chapters'] as $chapter)
                                <div class="chapter-item">
                                    <div class="chapter-header" data-chapter-id="{{ $chapter['id'] }}">
                                        <span>
                                            <i class="fa fa-chevron-right toggle-icon" id="chapter-icon-{{ $chapter['id'] }}"></i>
                                            {{ $chapter['title'] }}
                                        </span>
                                        <span class="badge bg-secondary">{{ count($chapter['lessons']) }}</span>
                                    </div>
                                    <div class="chapter-content" id="chapter-{{ $chapter['id'] }}" style="display: none;">
                                        @foreach($chapter['lessons'] as $lesson)
                                            <div class="lesson-item" data-lesson-id="{{ $lesson['id'] }}">
                                                <i class="fa {{ $lesson['has_files'] ? 'fa-file-text' : 'fa-book' }} lesson-icon"></i>
                                                <span>{{ $lesson['title'] }}</span>
                                                @if($lesson['has_files'])
                                                    <i class="fa fa-paperclip ms-auto"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Section Quiz -->
                @if($formation->quizzes->count() > 0)
                    <div class="quiz-section">
                        <h6><i class="fa fa-question-circle me-2"></i>Quiz disponibles</h6>
                        @foreach($formation->quizzes as $quiz)
                            <div class="quiz-item">
                                <div>
                                    <strong>{{ $quiz->title }}</strong><br>
                                    <small class="text-muted">
                                        Type: {{ $quiz->type == 'final' ? 'Quiz final' : 'Test de niveau' }}
                                    </small>
                                </div>

                                @if($canTakeQuiz)
                                    @if($quiz->type == 'placement' )

                                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->id() == $formation->user_id)
                                            <form action="{{ route('admin.quizzes.show', $quiz->id) }}" method="GET" style="display: inline;">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-eye"></i> Voir détails
                                                </button>
                                            </form>
                                        @elseif(auth()->user()->hasRole('etudiant'))
                                            <form action="{{ route('quizzes.start', $quiz->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-play me-1"></i> Commencer
                                                </button>
                                            </form>
                                        @endif

                                    @elseif($quiz->type == 'final')
                                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->id() == $formation->user_id)

                                            <form action="{{ route('admin.quizzes.show', $quiz->id) }}" method="GET" style="display: inline;">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-eye"></i> Voir détails
                                                </button>
                                            </form>
                                        @elseif(auth()->user()->hasRole('etudiant') )
                                            @if($hasPaidAccess)
                                                <form action="{{ route('quizzes.start', $quiz->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa fa-play me-1"></i>Commencer
                                                    </button>
                                                </form>
                                            @else

                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fa fa-lock me-1"></i> Verrouillé
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fa fa-lock me-1"></i>Verrouillé
                                    </button>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if(!auth()->check())
    @php($hideAdminFooter = true)
    <style>
    .footer .container {
        padding-left: 50rem !important; /* Ajustez cette valeur selon vos besoins */
        padding-right: -100rem !important;
    }

    .container {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 2rem !important;
    }

    .content-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 10rem !important; /* ⇨ pousse un peu à droite */
    }
    </style>

    <style>
        .formations-container {
            margin-bottom: 0.6rem;
            padding-bottom: 0.6rem;
        }
    </style>

    <style>
        .footer {
            background: #1f2937;
            color: white;
            /* padding: 2rem 0 2rem; */
            width: 100vw !important;
            margin: 0 !important;
            /* margin-left: -290px !important; */
            /* margin-right: 5px !important; */
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #1a202c;
            /* display: none;  */
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 5rem;
            margin: 0 !important;
            padding: 0 5rem !important;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #60a5fa;
        }

        .footer-section p,
        .footer-section a {
            color: #d1d5db;
            text-decoration: none;
            line-height: 1.8;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #60a5fa;
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            padding: 1rem 2rem 0 2rem !important;
            text-align: right;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom-left {
            flex: 1;
            text-align: right;
            min-width: 0;
            padding-right: 2rem;
        }

        .footer-bottom-right {
            flex: 1;
            padding-right: -5rem;
            margin-right: 3rem;
        }

        @media (max-width: 768px) {
            .footer-content {
                padding: 0 1rem;
            }

            .footer-bottom {
                padding: 2rem 1rem 0 1rem;
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .footer-bottom-left,
            .footer-bottom-right {
                text-align: center;
            }
        }
    </style>

@endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/MonJs/formations/training-detail.js') }}" defer></script>
@endpush

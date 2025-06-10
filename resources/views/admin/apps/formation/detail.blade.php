@extends('layouts.admin.master')

@section('title', 'Détails de la Formation')

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

<div class="container-fluid training-detail-container">

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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/MonJs/formations/training-detail.js') }}" defer></script>
@endpush

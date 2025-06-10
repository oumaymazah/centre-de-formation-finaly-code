<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // AJOUT: Import manquant

class TrainingDetailController extends Controller
{
    public function show($id)
    {
        // Récupérer la formation avec ses relations
        $formation = Training::with([
            'courses.chapters.lessons.files',
            'quizzes' => function($query) {
                $query->orderBy('type', 'desc'); // test_niveau en premier, puis final
            },
            'user',
            'category',
            'feedbacks'
        ])->findOrFail($id);
         // Calculer la note moyenne
        $averageRating = 0;
        $totalFeedbacks = count($formation->feedbacks);

        if ($totalFeedbacks > 0) {
            $sumRatings = $formation->feedbacks->sum('rating_count');
            $averageRating = $sumRatings / $totalFeedbacks;
        }
       
        // Vérifier si l'utilisateur a payé la formation
        $hasPaidAccess = false;
        $canTakeQuiz = false;

        if (Auth::check()) {
            $userId = Auth::id();

            // Vérifier si l'utilisateur a une réservation validée pour cette formation
            $reservation = Reservation::where('user_id', $userId)
                ->where('status', 1)
                ->whereJsonContains('training_data', (string)$id)
                ->first();

            $hasPaidAccess = $reservation !== null;
            $canTakeQuiz = true; // Connecté peut passer les quiz
        }

        // Organiser les données de structure
        $courseStructure = $this->organizeFormationStructure($formation);

        return view('admin.apps.formation.detail', compact(
            'formation',
            'courseStructure',
            'hasPaidAccess',
            'canTakeQuiz',
            'averageRating',
            'totalFeedbacks'
        ));
    }






public function getLessonContent(Request $request, $lessonId)
{
    try {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour accéder au contenu.',
                'requires_auth' => true
            ]);
        }

        $lesson = \App\Models\Lesson::with(['files', 'chapter.course.training'])->findOrFail($lessonId);

        if (!$lesson->chapter || !$lesson->chapter->course || !$lesson->chapter->course->training) {
            return response()->json([
                'success' => false,
                'message' => 'Structure de données incomplète pour cette leçon.'
            ], 500);
        }

        $formationId = $lesson->chapter->course->training->id;
        $userId = Auth::id();
        $user = Auth::user();

        $isSuperAdmin = $user->hasRole('super-admin');
        $isAdmin = $user->hasRole('admin');
        $isProfessor = $user->hasRole('professeur');

        $hasAccess = false;
        if ($isSuperAdmin || $isAdmin ) {
            $hasAccess = true;
        } elseif ($isProfessor) {
            $hasAccess = $lesson->chapter->course->Training->user_id == $userId;
        } else {
            $hasAccess = Reservation::where('user_id', $userId)
                ->where('status', 1)
                ->whereJsonContains('training_data', (string)$formationId)
                ->exists();
            $isFree = $lesson->chapter->course->training->type === 'gratuite';
            $hasAccess = $hasAccess || $isFree;
        }

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas l\'autorisation d\'accéder à ce contenu.',
                'requires_payment' => !$isSuperAdmin && !$isAdmin && !$isProfessor
            ]);
        }

        $organizedFiles = $this->organizeFilesByType($lesson->files);

        $links = [];
        if ($lesson->link) {
            \Log::info("Raw link data for lesson {$lessonId}: " . json_encode($lesson->link)); // Débogage
            try {
                $rawLinks = is_string($lesson->link) ? json_decode($lesson->link, true) : $lesson->link;
                if (is_array($rawLinks)) {
                    foreach ($rawLinks as $rawLink) {
                        if ($rawLink && filter_var(str_replace('\/', '/', $rawLink), FILTER_VALIDATE_URL)) {
                            $links[] = str_replace('\/', '/', $rawLink);
                        }
                    }
                } elseif (filter_var(str_replace('\/', '/', $rawLinks), FILTER_VALIDATE_URL)) {
                    $links[] = str_replace('\/', '/', $rawLinks);
                }
            } catch (\Exception $e) {
                \Log::warning("Erreur lors du traitement des liens pour la leçon {$lessonId}: {$e->getMessage()}");
            }
        }
        \Log::info("Processed links for lesson {$lessonId}: " . json_encode($links)); // Débogage

        return response()->json([
            'success' => true,
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'duration' => $lesson->duration,
                'description' => $lesson->description,
                'content' => $lesson->content,
                'links' => $links,
                'files' => $organizedFiles
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error("Erreur lors de la récupération du contenu de la leçon {$lessonId}: {$e->getMessage()}");
        return response()->json([
            'success' => false,
            'message' => 'Leçon non trouvée ou erreur serveur.'
        ], 404);
    }
}
    // AJOUT: Méthode pour tracker les vues (appelée par JavaScript)
    public function trackLessonView(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Non authentifié']);
            }

            $lessonId = $request->input('lesson_id');
            $userId = Auth::id();

            // Ici vous pouvez enregistrer la vue dans une table de tracking
            // Par exemple: LessonView::create(['user_id' => $userId, 'lesson_id' => $lessonId]);

            // Pour l'instant, on retourne juste un succès
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de tracking']);
        }
    }

    private function organizeFormationStructure($formation)
    {
        $structure = [];

        foreach ($formation->courses as $course) {
            $courseData = [
                'id' => $course->id,
                'title' => $course->title,
                'chapters' => []
            ];

            // CORRECTION: Utiliser la bonne relation (chapters au lieu de Chapters)
            foreach ($course->chapters as $chapter) {
                $chapterData = [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'lessons' => []
                ];

                foreach ($chapter->lessons as $lesson) {
                    $chapterData['lessons'][] = [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'has_files' => $lesson->files->count() > 0
                    ];
                }

                $courseData['chapters'][] = $chapterData;
            }

            $structure[] = $courseData;
        }

        return $structure;
    }

    private function organizeFilesByType($files)
    {
        $organized = [
            'videos' => [],
            'documents' => [],
            'images' => [],
            'others' => []
        ];

        foreach ($files as $file) {
            $fileData = [
                'id' => $file->id,
                'name' => $file->name,
                'file_path' => $file->file_path,
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
                'formatted_size' => $this->formatFileSize($file->file_size)
            ];

            // Catégoriser les fichiers
            if (in_array(strtolower($file->file_type), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'])) {
                $organized['videos'][] = $fileData;
            } elseif (in_array(strtolower($file->file_type), ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx', 'xls', 'xlsx'])) {
                $organized['documents'][] = $fileData;
            } elseif (in_array(strtolower($file->file_type), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
                $organized['images'][] = $fileData;
            } else {
                $organized['others'][] = $fileData;
            }
        }

        return $organized;
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

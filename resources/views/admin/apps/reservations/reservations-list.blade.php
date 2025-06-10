<div class="container-fluid px-0">
    <div class="card rounded-0 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3 rounded-0 mb-4">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-white p-2 me-3">
                         <i class="fas fa-calendar-check text-primary fa-lg"></i>
                </div>
                <h3 class="fw-bold mb-0">Gestion des Réservations</h3>
            </div>
         </div>
                    <div class="card-body pb-0">

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="stat-card bg-white shadow-sm border border-light rounded-3 flex-grow-1">
                                            <div class="d-flex p-3">
                                                <div class="stat-icon-container rounded-circle bg-primary-ultra-light p-3 me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-clipboard-list fa-lg text-primary"></i>
                                                </div>
                                                <div>
                                                    <h3 class="fs-4 fw-bold mb-0">{{ $reservations->total() }}</h3>
                                                    <p class="text-muted mb-0">Total des réservations</p>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $studentsWithReservationsCollection = collect($studentsWithReservations);
                                        @endphp
                                        <div class="stat-card bg-white shadow-sm border border-light rounded-3 flex-grow-1">
                                            <div class="d-flex p-3">
                                                <div class="stat-icon-container rounded-circle bg-primary-ultra-light p-3 me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-check-circle fa-lg text-primary"></i>
                                                </div>
                                                <div>
                                                    <h3 class="fs-4 fw-bold mb-0">{{ $studentsWithReservationsCollection->where('status', 1)->count() }}</h3>
                                                    <p class="text-muted mb-0">Réservations confirmées</p>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        <div class="card shadow-sm mb-3">


                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <select class="form-select filter-select" id="reservation-status-filter" aria-label="Filtrer par statut">
                                                <option value="">Tous les statuts</option>
                                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>En attente</option>
                                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Confirmées</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="Rechercher par ID réservation,ou téléphone..."
                                                id="reservation-search-input" value="{{ request('search') ?? '' }}">

                                        </div>
                                    </div>


                                    <div class="col-md-2">

                                        <button class="btn btn-sm btn-light border-0 shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center" id="reset-reservation-filters" data-bs-toggle="tooltip" title="Effacer tous les filtres">
                                            <i class="fas fa-undo-alt text-primary"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">



                        <div class="table-responsive m-0">
                            <table id="reservations-table" class="table table-borderless compact-table m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th  width="20%" class="border-top-0 ">Code</th>
                                        <th class="border-top-0">Nom Complet</th>
                                        <th class="border-top-0">Téléphone</th>
                                        <th class="border-top-0">Statut</th>
                                        <th class="border-top-0">Date de paiement</th>
                                        <th class="border-top-0 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($studentsWithReservations as $student)
                                    <tr data-reservation-id="{{ $student['reservation_id'] }}" >
                                        <td class="fw-bold">{{ $student['reservation_id'] }}</td>
                                        <td>{{ $student['nom'] }} {{ $student['prenom'] }}</td>
                                        <td>{{ $student['telephone'] }}</td>
                                        <td>
    <span class="badge {{ $student['status'] == 0 ? 'bg-danger' : 'bg-primary' }} px-2 py-1 d-inline-flex align-items-center" style="font-size: 0.8rem; border-radius: 4px; font-weight: 500; text-transform: none;">
        <i class="fas {{ $student['status'] == 0 ? 'fa-clock' : 'fa-check-circle' }} me-1" style="font-size: 0.75rem;"></i>
        {{ $student['status_text'] }}
    </span>
</td>

                                        <td>
                                            @if($student['payment_date'])
                                                {{ \Carbon\Carbon::parse($student['payment_date'])->format('d/m/Y H:i') }}
                                            @else
                                            <span class="text-muted" style="margin-left: 70px"> - </span>

                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">

                                                @if($student['status'] == 0)
                                                    <form class="reservation-status-form" method="POST" action="{{ route('reservations.updateStatus') }}">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Valider cette réservation">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form class="reservation-status-form" method="POST" action="{{ route('reservations.updateStatus') }}">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit" class="btn btn-sm py-1 px-2" style="background-color: #907b75; border-color: #907b75; color: white;" title="Annuler la validation">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif


                                                <div class="dropdown dropdown-user-actions">
                                                    <button class="btn btn-sm btn-light dropdown-toggle py-1 px-2" type="button"
                                                            id="dropdownMenuButton-{{ $student['reservation_id'] }}" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-{{ $student['reservation_id'] }}">
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#formationsModal{{ $student['reservation_id'] }}">
                                                                <i class="fas fa-book-open me-2"></i> Voir formations ({{ count($student['formations']) }})
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item delete-reservation" href="#" data-url="{{ route('admin.reservations.destroy',$student['reservation_id']) }}">
                                                                <i class="fas fa-trash me-2"></i> Supprimer
                                                            </a>

                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>

                                    @empty
                                    <tr>
                                        <td colspan="7" class="empty-state">
                                            <div class="empty-content">
                                                <i class="fas fa-search-minus"></i>
                                                <h3>Aucune Reservation trouvée</h3>
                                                <p>Modifiez vos critères de recherche ou essayez plus tard</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($reservations->hasPages())
                            <div class="pagination-wrapper mt-4">
                                <div class="pagination-info text-muted small mb-2">
                                    <i class="fas fa-file-alt me-1"></i> Affichage de
                                    <span class="fw-bold">{{ $reservations->firstItem() }}</span>
                                    à <span class="fw-bold">{{ $reservations->lastItem() }}</span>
                                    sur <span class="fw-bold">{{ $reservations->total() }}</span> réservations
                                </div>

                                <div class="pagination-controls">
                                    <ul class="pagination custom-pagination justify-content-center">
                                        {{-- Lien Précédent --}}
                                        <li class="page-item {{ $reservations->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                            href="{{ $reservations->appends(request()->except('page'))->previousPageUrl() }}"
                                            aria-label="Précédent"
                                            @if(!$reservations->onFirstPage()) onclick="return paginateReservations(event)" @endif>
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        {{-- Numéros de page --}}
                                        @foreach ($reservations->getUrlRange(max(1, $reservations->currentPage() - 2), min($reservations->lastPage(), $reservations->currentPage() + 2)) as $page => $url)
                                            <li class="page-item {{ $reservations->currentPage() == $page ? 'active' : '' }}">
                                                <a class="page-link"
                                                href="{{ $url }}"
                                                onclick="return paginateReservations(event)">
                                                    {{ $page }}
                                                </a>
                                            </li>
                                        @endforeach

                                        {{-- Lien Suivant --}}
                                        <li class="page-item {{ !$reservations->hasMorePages() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                            href="{{ $reservations->appends(request()->except('page'))->nextPageUrl() }}"
                                            aria-label="Suivant"
                                            @if($reservations->hasMorePages()) onclick="return paginateReservations(event)" @endif>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
@foreach($studentsWithReservations as $student)
<div class="modal fade" id="formationsModal{{ $student['reservation_id'] }}" tabindex="-1" aria-labelledby="formationsModalLabel{{ $student['reservation_id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title d-flex align-items-center" id="formationsModalLabel{{ $student['reservation_id'] }}">
                    <span class="modal-icon-container bg-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-graduation-cap text-primary"></i>
                    </span>
                    Formations réservées <span class="badge bg-white text-primary ms-2"><strong>ID: {{ $student['reservation_id'] }}</strong></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if(count($student['formations']) > 0)
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-list-alt text-white"></i>
                                        </div>
                                        <div>
                                            <p class="card-text mb-0 text-dark small">Nombre total de formations</p>
                                            <h6 class="card-title mb-0 text-muted">{{ count($student['formations']) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-calendar-alt text-white"></i>
                                        </div>
                                        <div>
                                            <p class="card-text mb-0 text-dark small">Date de réservation</p>
                                            <h6 class="card-title mb-0  text-muted">{{ \Carbon\Carbon::parse($student['reservation_date'])->format('d/m/Y') }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                      @php
            $totalOriginal = 0;
            $totalDiscount = 0;
            $totalFinal = 0;
            $hasAnyDiscount = false;

            // Vérification s'il y a des remises
            foreach($student['formations'] as $formation) {
                $discount = $formation['discount'] ?? 0;
                if ($discount > 0) {
                    $hasAnyDiscount = true;
                    break;
                }
            }
            @endphp
{{-- <div class="table-responsive rounded shadow-sm">
    <table class="table mb-0">
        <thead class="bg-light">
            <tr>
                <th class="px-3 py-3 border-0">Formation</th>
                <th class="px-3 py-3 border-0 text-end">Prix</th>
                @if($hasAnyDiscount)
                    <th class="px-3 py-3 border-0 text-end">Remise</th>
                    <th class="px-3 py-3 border-0 text-end">Prix final</th>
                @endif
            </tr>
        </thead>
        <tbody>


            @foreach($student['formations'] as $formation)
                @php
                $originalPrice = $formation['price'];
                $discount = $formation['discount'] ?? 0;
                $discountAmount = 0;
                $finalPrice = $originalPrice;

                if ($discount > 0) {
                    $discountAmount = ($originalPrice * $discount) / 100;
                    $finalPrice = $originalPrice - $discountAmount;
                }

                $totalOriginal += $originalPrice;
                $totalDiscount += $discountAmount;
                $totalFinal += $finalPrice;
                @endphp

                <tr>
                    <td class="px-3 py-3">
                        <div class="d-flex align-items-center">
                            <span>{{ $formation['title'] }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-3 text-end">{{ number_format($originalPrice, 2) }} Dt</td>
                    @if($hasAnyDiscount)
                        <td class="px-3 py-3 text-end">
                            @if($discount > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                    {{ $discount }}%
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-end fw-bold">{{ number_format($finalPrice, 2) }} Dt</td>
                    @endif
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="bg-primary">
                <th class="px-3 py-3 border-0 text-white" colspan="1">Total</th>
                <th class="px-3 py-3 border-0 text-white text-end">{{ number_format($totalOriginal, 2) }} Dt</th>
                @if($hasAnyDiscount)
                    <th class="px-3 py-3 border-0 text-white text-end">
                        @if($totalDiscount > 0 && $totalOriginal > 0)
                            <span class="badge bg-danger bg-opacity-10 text-white px-2 py-1">
                                {{ number_format(($totalDiscount / $totalOriginal) * 100, 2) }}%
                            </span>
                        @else
                            <span class="text-white">-</span>
                        @endif
                    </th>
                    <th class="px-3 py-3 border-0 text-white text-end">{{ number_format($totalFinal, 2) }} Dt</th>
                @endif
            </tr>
        </tfoot>
    </table>
</div>
                     --}}

<!-- Remplacez la partie du tableau dans le modal par ce code -->
{{-- <div class="table-responsive rounded shadow-sm">
    <table class="table mb-0">
        <thead class="bg-light">
            <tr>
                <th class="px-3 py-3 border-0">Formation</th>
                <th class="px-3 py-3 border-0 text-center">Dates</th>
                <th class="px-3 py-3 border-0 text-center">Prix</th>
                @if($hasAnyDiscount)
                    <th class="px-3 py-3 border-0 text-center">Remise</th>
                    <th class="px-3 py-3 border-0 text-center">Prix final</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($student['formations'] as $formation)
                @php
                $originalPrice = $formation['price'];
                $discount = $formation['discount'] ?? 0;
                $discountAmount = 0;
                $finalPrice = $originalPrice;

                if ($discount > 0) {
                    $discountAmount = ($originalPrice * $discount) / 100;
                    $finalPrice = $originalPrice - $discountAmount;
                }

                $totalOriginal += $originalPrice;
                $totalDiscount += $discountAmount;
                $totalFinal += $finalPrice;
                @endphp

                <tr>
                    <td class="px-3 py-3">
                        <div class="d-flex align-items-center">
                            <span>{{ $formation['title'] }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-3 text-center">
                        @if($formation['start_date'] && $formation['end_date'])
                            <div class="date-range">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge bg-light border text-dark small mb-1">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($formation['start_date'])->format('d/m/Y') }}
                                    </span>
                                    <i class="fas fa-arrow-down text-muted" style="font-size: 0.7rem;"></i>
                                    <span class="badge bg-light border text-dark small mt-1">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        {{ \Carbon\Carbon::parse($formation['end_date'])->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <span class="text-muted small">
                                <i class="fas fa-calendar-times me-1"></i>
                                Dates non définies
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center">{{ number_format($originalPrice, 2) }} Dt</td>
                    @if($hasAnyDiscount)
                        <td class="px-3 py-3 text-center">
                            @if($discount > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                    {{ $discount }}%
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center fw-bold">{{ number_format($finalPrice, 2) }} Dt</td>
                    @endif
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="bg-primary">
                <th class="px-3 py-3 border-0 text-white">Total</th>
                <th class="px-3 py-3 border-0 text-white text-center">-</th>
                <th class="px-3 py-3 border-0 text-white text-center">{{ number_format($totalOriginal, 2) }} Dt</th>
                @if($hasAnyDiscount)
                    <th class="px-3 py-3 border-0 text-white text-center">
                        @if($totalDiscount > 0 && $totalOriginal > 0)
                            <span class="badge bg-danger bg-opacity-10 text-white px-2 py-1">
                                {{ number_format(($totalDiscount / $totalOriginal) * 100, 2) }}%
                            </span>
                        @else
                            <span class="text-white">-</span>
                        @endif
                    </th>
                    <th class="px-3 py-3 border-0 text-white text-center">{{ number_format($totalFinal, 2) }} Dt</th>
                @endif
            </tr>
        </tfoot>
    </table>
</div>    --}}
<!-- Remplacez la partie du tableau dans le modal par ce code -->
<div class="table-responsive rounded shadow-sm">
    <table class="table mb-0">
        <thead class="bg-light">
            <tr>
                <th class="px-3 py-3 border-0">Formation</th>
                <th class="px-3 py-3 border-0 text-center">Date début</th>
                <th class="px-3 py-3 border-0 text-center">Date fin</th>
                <th class="px-3 py-3 border-0 text-center">Prix</th>
                @if($hasAnyDiscount)
                    <th class="px-3 py-3 border-0 text-center">Remise</th>
                    <th class="px-3 py-3 border-0 text-center">Prix final</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($student['formations'] as $formation)
                @php
                $originalPrice = $formation['price'];
                $discount = $formation['discount'] ?? 0;
                $discountAmount = 0;
                $finalPrice = $originalPrice;

                if ($discount > 0) {
                    $discountAmount = ($originalPrice * $discount) / 100;
                    $finalPrice = $originalPrice - $discountAmount;
                }

                $totalOriginal += $originalPrice;
                $totalDiscount += $discountAmount;
                $totalFinal += $finalPrice;
                @endphp

                <tr>
                    <td class="px-3 py-3">
                        <div class="d-flex align-items-center">
                            <span>{{ $formation['title'] }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-3 text-center">
                        @if($formation['start_date'])
                            <span class="date-badge bg-light">
                                <i class="fas fa-play"></i>
                                {{ \Carbon\Carbon::parse($formation['start_date'])->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="date-badge date-not-set">
                                <i class="fas fa-question-circle"></i>
                                Non définie
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center">
                        @if($formation['end_date'])
                            <span class="date-badge bg-light">
                                <i class="fas fa-stop"></i>
                                {{ \Carbon\Carbon::parse($formation['end_date'])->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="date-badge date-not-set">
                                <i class="fas fa-question-circle"></i>
                                Non définie
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center">{{ number_format($originalPrice, 2) }} Dt</td>
                    @if($hasAnyDiscount)
                        <td class="px-3 py-3 text-center">
                            @if($discount > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                    {{ $discount }}%
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-center fw-bold">{{ number_format($finalPrice, 2) }} Dt</td>
                    @endif
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="bg-primary">
                <th class="px-3 py-3 border-0 text-white">Total</th>
                <th class="px-3 py-3 border-0 text-white text-center">-</th>
                <th class="px-3 py-3 border-0 text-white text-center">-</th>
                <th class="px-3 py-3 border-0 text-white text-center">{{ number_format($totalOriginal, 2) }} Dt</th>
                @if($hasAnyDiscount)
                    <th class="px-3 py-3 border-0 text-white text-center">
                        @if($totalDiscount > 0 && $totalOriginal > 0)
                            <span class="badge bg-danger bg-opacity-10 text-white px-2 py-1">
                                {{ number_format(($totalDiscount / $totalOriginal) * 100, 2) }}%
                            </span>
                        @else
                            <span class="text-white">-</span>
                        @endif
                    </th>
                    <th class="px-3 py-3 border-0 text-white text-center">{{ number_format($totalFinal, 2) }} Dt</th>
                @endif
            </tr>
        </tfoot>
    </table>
</div>
                @else
                    <div class="text-center py-5">
                        <div class="empty-state-icon bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-info-circle fa-2x text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-1">Aucune formation</h5>
                        <p class="text-muted small mb-0">Cette réservation ne contient aucune formation.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer border-0 bg-light p-3">

                <div class="ms-auto">
                    <div class="price-badge bg-primary text-white px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-tags me-1"></i>
                        Prix Total: <span class="fw-bold">{{ number_format($totalFinal ?? 0, 2) }} Dt</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    /* Styles pour l'affichage des dates dans le modal - Version colonnes séparées */
.date-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.6rem;
    /* border-radius: 0.375rem; */
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    min-width: 90px;
    justify-content: center;
}

.date-badge.bg-light {
    background-color: transparent !important;
    border: none !important;
    color: #495057 !important;
        font-size: 1rem;


}

.date-badge.date-not-set {
    background-color: #fef3cd !important;
    border: 1px solid #ffd60a !important;
    color: #856404 !important;
}

.date-badge i {
    font-size: 0.7rem;
}

/* Correction pour l'alignement avec les nouvelles colonnes */
.table tbody td:nth-child(2),
.table tbody td:nth-child(3),
.table tbody td:nth-child(4),
.table tbody td:nth-child(5),
.table tbody td:nth-child(6),
.table tfoot th:nth-child(2),
.table tfoot th:nth-child(3),
.table tfoot th:nth-child(4),
.table tfoot th:nth-child(5),
.table tfoot th:nth-child(6) {
    text-align: center !important;
    vertical-align: middle !important;
}

/* Responsive design pour les dates */
@media (max-width: 768px) {
    .date-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.4rem;
        min-width: 80px;
    }

    .date-badge i {
        font-size: 0.65rem;
    }
}

/* Animation hover pour les badges de date */
/* .date-badge:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease;
} */
</style>

<style>


    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --primary-light: #6184ff;
        --primary-ultra-light: #eef1ff;
        --secondary: #3f37c9;
        --success: #4c89e8;
        --success-ultra-light: #e6f3ff;
        --danger: #f87171;
        --danger-ultra-light: #fee2e2;
        --warning: #fbbf24;
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
        --text-primary: #333;
        --text-secondary: #6b7280;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        --radius-sm: 0.25rem;
        --radius: 0.5rem;
        --radius-lg: 0.75rem;
        --transition: all 0.3s ease;
    }

    .dropdown-toggle::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 10rem;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    .dropdown-item {
        padding: 0.35rem 1.5rem;
        font-size: 0.875rem;
    }
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.5em;
        cursor: pointer;
    }
    .btn-light {
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }
    .dropdown-toggle {
        padding: 0.25rem 0.5rem;
    }
    .badge {
        margin-right: 3px;
    }

    /* Modifications pour corriger le problème d'affichage du dropdown */
    .dropdown-user-actions {
        position: relative;
    }
    .dropdown-menu-end {
        right: 0;
        left: auto !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
        /* Pagination Styles */
    .empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
        }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 400px;
        margin: 0 auto;
    }

    .empty-content i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-content h3 {
        margin: 0 0 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .empty-content p {
        color: var(--text-secondary);
        margin: 0;
        font-size: 0.95rem;
    }

    .pagination-wrapper {
        padding: 0.85rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-info {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .highlight {
        color: var(--primary);
        font-weight: 600;
    }

    .pagination-controls {
        display: flex;
        justify-content: flex-end;
    }

    .custom-pagination {
        display: flex;
        gap: 0.25rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-item {
        margin: 0;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: var(--radius);
        font-size: 0.9rem;
        transition: var(--transition);
        border: 1px solid var(--border);
        background-color: white;
        color: var(--text-secondary);
        text-decoration: none;
    }

    .page-item.active .page-link {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-item:not(.active) .page-link:hover {
        background-color: var(--primary-ultra-light);
        color: var(--primary);
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        pointer-events: none;
    }
     .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }

    .stat-icon-container {
        width: 50px;
        height: 50px;
    }


    .modal-icon-container {
        width: 32px;
        height: 32px;
    }

    /* Animation pour la modale */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* Styles pour le tableau dans la modale */
    .table-hover tbody tr:hover {
        background-color: var(--primary-ultra-light);
    }

    /* Style pour le badge de prix total */
    .price-badge {
        display: inline-block;
        font-size: 0.95rem;
    }

    /* Style pour les états vides */
    .empty-state-icon {
        transition: transform 0.3s ease;
    }

    .empty-state-icon:hover {
        transform: scale(1.05);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .modal-footer {
            flex-direction: column-reverse;
            gap: 1rem;
        }

        .modal-footer .ms-auto {
            margin-left: 0 !important;
            width: 100%;
        }

        .price-badge {
            width: 100%;
            text-align: center;
        }
    }

</style>
<style>
    /* Reset des marges et paddings */
    .container-fluid.px-0 {
        padding-left: 0;
        padding-right: 0;
    }

    .card.rounded-0 {
        border-radius: 0 !important;
    }

    .card-body.p-0 {
        padding: 0 !important;
    }



    /* Styles pour le tableau */
    .compact-table {
        width: 100% !important;
        margin: 0 !important;
    }

    .compact-table th,
    .compact-table td {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .compact-table th {
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .compact-table th.border-top-0 {
        border-top: none !important;
    }

    /* Largeurs des colonnes */
    .compact-table th:nth-child(1) { width: 4%; }
    .compact-table th:nth-child(2) { width: 15%; }
    .compact-table th:nth-child(3) { width: 10%; }
    .compact-table th:nth-child(4) { width: 16%; }
    .compact-table th:nth-child(5) { width: 8%; }
    .compact-table th:nth-child(6) { width: 12%; }
    .compact-table th:nth-child(7) { width: 18%; }

    .email-cell {
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }



    /* Styles pour les petits écrans */
    @media (max-width: 992px) {
        .compact-table th:nth-child(3),
        .compact-table td:nth-child(3),
        .compact-table th:nth-child(4),
        .compact-table td:nth-child(4) {
            display: none;
        }

        .compact-table th:nth-child(1) { width: 8%; }
        .compact-table th:nth-child(2) { width: 30%; }
        .compact-table th:nth-child(5) { width: 15%; }
        .compact-table th:nth-child(6) { width: 20%; }
        .compact-table th:nth-child(7) { width: 27%; }
    }
    /* 1. Modifier le background de la remise (badge rouge) */
.badge.bg-danger.bg-opacity-10.text-danger {
    background-color: #fef2f2 !important; /* Background rouge très clair */
    color: #dc2626 !important; /* Texte rouge foncé pour meilleure lisibilité */
    border: 1px solid #fecaca; /* Bordure rouge claire */
}
    /* Styles pour les badges */
    .badge {
        font-weight: 20;
        letter-spacing: 0.5px;
        /* text-transform: uppercase; */
        font-size: 0.5rem;
    }

    /* Styles pour les boutons */
    .btn-sm.py-1.px-2 {
        padding: 0.25rem 0.5rem;
    }



    /* Suppression des bordures du tableau */
    .table-borderless td,
    .table-borderless th {
        border: none;
    }

    /* Empêcher le défilement lors de l'ouverture du dropdown */
    body.dropdown-no-scroll {
        overflow: hidden !important;
    }


</style>


<style>
    /* Correction pour l'alignement des remises et taille du texte */

/* 1. Améliorer l'alignement des badges de remise dans le tableau */
.table tbody td:nth-child(3),
.table tfoot th:nth-child(3) {
    text-align: center !important;
    vertical-align: middle !important;
}

/* 2. Styles pour les badges de remise - taille plus grande */
.badge.bg-danger.bg-opacity-10.text-danger {
    background-color: #fef2f2 !important;
    color: #dc2626 !important;
    border: 1px solid #fecaca;
    font-size: 0.85rem !important; /* Augmenté de 0.75rem à 0.85rem */
    font-weight: 500 !important;
    padding: 0.35rem 0.7rem !important; /* Padding plus généreux */
    border-radius: 0.375rem !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 50px; /* Largeur minimum pour uniformité */
}

/* 3. Badge de remise dans le footer - même style mais en blanc sur fond primary */
.table tfoot .badge.bg-danger.bg-opacity-10.text-white {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    font-size: 0.85rem !important;
    font-weight: 500 !important;
    padding: 0.35rem 0.7rem !important;
    border-radius: 0.375rem !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 50px;
}

/* 4. Augmenter la taille du texte général du tableau */
.table tbody td,
.table thead th,
.table tfoot th {
    font-size: 0.9rem !important; /* Augmenté de la taille par défaut */
    padding: 0.75rem !important; /* Padding plus généreux */
}

/* 5. Texte "Total" plus grand et centré */
.table tfoot th:first-child {
    font-size: 1rem !important;
    font-weight: 600 !important;
    text-align: center !important;
}

/* 6. Prix plus visibles */
.table tbody td:nth-child(2),
.table tbody td:nth-child(4),
.table tfoot th:nth-child(2),
.table tfoot th:nth-child(4) {
    font-size: 0.95rem !important;
    font-weight: 500 !important;
}

/* 7. Assurer l'alignement central pour toutes les colonnes de prix et remise */
.table tbody td:nth-child(2),
.table tbody td:nth-child(3),
.table tbody td:nth-child(4),
.table tfoot th:nth-child(2),
.table tfoot th:nth-child(3),
.table tfoot th:nth-child(4) {
    text-align: center !important;
    vertical-align: middle !important;
}

/* 8. Style spécial pour le texte "-" quand il n'y a pas de remise */
.table tbody td:nth-child(3) .text-muted {
    font-size: 0.9rem !important;
    font-weight: 500 !important;
}

.card {
    position: none !important;

  /* position: relative; */
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
    /* display: none !important;  */

  -webkit-box-orient: vertical !important;
  -webkit-box-direction: normal !important;
      -ms-flex-direction: column !important;
          flex-direction: column !important;
  min-width: 0;
  word-wrap: break-word;
  /* background-color: #fff; */
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.125);
  /* border-radius: 0.25rem;  */
}
</style>

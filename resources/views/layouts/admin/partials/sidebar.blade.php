
@auth

<header class="main-nav">

    <div class="sidebar-user text-center">
        <a class="setting-primary" href="{{ route('profile.parametre') }}">
            <i data-feather="settings"></i>
        </a>
        <div class="mb-4">
            <div class="avatar-circle text-white mx-auto" style="background-color:  #2B6ED4; width: 80px; height: 80px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 28px; font-weight: bold;">
                <span>{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->lastname, 0, 1) }}</span>
            </div>
        </div>

        <a style="color:  #2B6ED4">
            <h6 class="mt-3 f-14 f-w-600">{{auth()->user()->name}} {{auth()->user()->lastname}} </h6>
        </a>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>



                    <li class="sidebar-main-title">
                        <div>
                            <h6>Contenu Educatif</h6>
                        </div>
                    </li>
                    <li class="dropdown">
                          @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))

                         <a class="nav-link menu-title {{ prefixActive('/categorie') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Categories </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/categorie') }};">
                             <li><a href="{{ route('categories') }}" class="{{routeActive('categories')}}">Liste de Categories</a></li>
                             <li><a href="{{ route('categoriecreate') }}" class="{{routeActive('categoriecreate')}}">Nouvelle Catégorie </a></li>
                         </ul>
                        @endif

                         {{-- zedtouu  ta formation --}}
                         <a class="nav-link menu-title {{ prefixActive('/formation') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Formations </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/formation') }};">
                             <li><a href="{{ route('formations') }}" class="{{routeActive('formations')}}">Liste de formations</a></li>
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))

                             <li><a href="{{ route('formationcreate') }}" class="{{routeActive('formationcreate')}}">Nouvelle Formation  </a></li>
                                @endif
                            </ul>

                         @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')|| auth()->user()->hasRole('professeur'))

                         {{-- cours --}}
                         <a class="nav-link menu-title {{ prefixActive('/cours') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Cours </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/cours') }};">
                             <li><a href="{{ route('cours') }}" class="{{routeActive('cours')}}">Liste de cours</a></li>
                             <li><a href="{{ route('courscreate') }}" class="{{routeActive('courscreate')}}">Nouveau Cours </a></li>
                         </ul>
                         {{-- chapitre --}}

                         <a class="nav-link menu-title {{ prefixActive('/chapitre') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Chapitres </span></a>
                         <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/chapitre') }};">
                             <li><a href="{{ route('chapitres') }}" class="{{routeActive('chapitres')}}">Liste de chapitres</a></li>
                             <li><a href="{{ route('chapitrecreate') }}" class="{{routeActive('chapitrecreate')}}">Nouveau chapitre </a></li>
                         </ul>

                          {{-- lesson --}}
                          <a class="nav-link menu-title {{ prefixActive('/lesson') }}" href="javascript:void(0)"><i data-feather="box"></i><span>Leçons </span></a>
                          <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/lesson') }};">
                              <li><a href="{{ route('lessons') }}" class="{{routeActive('lessons')}}">Liste de Leçons</a></li>
                              <li><a href="{{ route('lessoncreate') }}" class="{{routeActive('lessoncreate')}}">Nouvelle leçon </a></li>
                          </ul>
                        @endif
                        @hasanyrole('admin|super-admin')
                                <a class="nav-link menu-title {{ prefixActive('/quizzes') }}" href="javascript:void(0)">
                                    <i data-feather="box"></i>
                                    <span>Quiz</span>
                                </a>
                                <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/quizzes') }};">
                                    <li><a href="{{ route('admin.quizzes.index') }}" class="{{ routeActive('admin.quizzes.index') }}">Liste des Quiz</a></li>
                                    <li><a href="{{ route('admin.quizzes.create') }}" class="{{ routeActive('admin.quizzes.create') }}">Nouveau Quiz</a></li>
                                </ul>
                                <a class="nav-link menu-title {{ prefixActive('/feedbacks') }}" href="javascript:void(0)">
                                    <i data-feather="box"></i>
                                    <span>Avis</span>
                                </a>
                                <ul class="nav-submenu menu-content" style="display: {{ prefixBlock('/feedbacks') }};">
                                    <li><a href="{{ route('feedbacks') }}" class="{{ routeActive('feedbacks') }}">Liste des Avis</a></li>

                                </ul>
                        @endhasanyrole

                    </li>
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))

                        <li class="sidebar-main-title">
                            <div>
                                <h6 style="font-size: 16px; ">Administration des comptes</h6>
                            </div>
                        </li>
                        <li class="dropdown">

                                <a class="nav-link menu-title link-nav {{routeActive('contacts')}}" href="{{ route('contacts') }}"><i data-feather="list"></i><span>Gestion des utilisateurs</span></a>

                        </li>
                    @endif






                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
@endauth

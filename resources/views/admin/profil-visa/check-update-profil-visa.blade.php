@php
    // Déterminer l'étape suivante basée sur l'étape actuelle
    $etapeActuelle = $tabProfilVisa->etape ?? 1;
    $etapeNoms = [
        1 => 'informations-personnelles',
        2 => 'pieces-d-identite',
        3 => 'questionnaires-documents',
        4 => 'informations-importantes-a-savoir'
    ];

    // Si l'étape actuelle est complétée, aller à la suivante
    $etapeSuivante = $etapeActuelle;
    if ($etapeActuelle < 4) {
        $etapeSuivante = $etapeActuelle + 1;
    }

    $nomEtape = $etapeNoms[$etapeSuivante] ?? 'informations-personnelles';
    $typeProfil = $tabProfilVisa->type_profil_visa ?? 'tourisme';
@endphp

@if(is_null($tabProfilVisa->type_profil_visa) || $tabProfilVisa->type_profil_visa === '')
    <a href="{{ route('profil.visa.create') }}"
       class="btn btn-warning btn-sm text-dark mt-2 mb-2"
       title="Définir le type de profil visa">
        <i class="fa fa-pencil-alt"></i> Définir mon profil
    </a>
    @php($bgColor="warning")
@elseif($tabProfilVisa->type_profil_visa == "tourisme")
    <a href="https://psiafrica.ci/je-definis-mon-profil-visa/profil-{{ $typeProfil }}/{{ $nomEtape }}/{{ $tabProfilVisa->id*2812 }}/{{ sha1($tabProfilVisa->numero_profil_visa) }}"
       class="btn btn-success btn-sm text-white mt-2 mb-2"
       target="_blank"
       title="Continuer le profil visa - Étape {{ $etapeSuivante }}/4">
        <i class="fa fa-play-circle"></i> Continuer le profil
    </a>
    @php($bgColor="primary")
@elseif($tabProfilVisa->type_profil_visa == "mineur")
    <a href="https://psiafrica.ci/je-definis-mon-profil-visa/profil-{{ $typeProfil }}/{{ $nomEtape }}/{{ $tabProfilVisa->id*2812 }}/{{ sha1($tabProfilVisa->numero_profil_visa) }}"
       class="btn btn-success btn-sm text-white mt-2 mb-2"
       target="_blank"
       title="Continuer le profil visa - Étape {{ $etapeSuivante }}/4">
        <i class="fa fa-play-circle"></i> Continuer le profil
    </a>
    @php($bgColor="warning")
@elseif($tabProfilVisa->type_profil_visa == "etude")
    <a href="https://psiafrica.ci/je-definis-mon-profil-visa/profil-{{ $typeProfil }}/{{ $nomEtape }}/{{ $tabProfilVisa->id*2812 }}/{{ sha1($tabProfilVisa->numero_profil_visa) }}"
       class="btn btn-success btn-sm text-white mt-2 mb-2"
       target="_blank"
       title="Continuer le profil visa - Étape {{ $etapeSuivante }}/4">
        <i class="fa fa-play-circle"></i> Continuer le profil
    </a>
    @php($bgColor="info")
@elseif($tabProfilVisa->type_profil_visa == "travail")
    <a href="https://psiafrica.ci/je-definis-mon-profil-visa/profil-{{ $typeProfil }}/{{ $nomEtape }}/{{ $tabProfilVisa->id*2812 }}/{{ sha1($tabProfilVisa->numero_profil_visa) }}"
       class="btn btn-success btn-sm text-white mt-2 mb-2"
       target="_blank"
       title="Continuer le profil visa - Étape {{ $etapeSuivante }}/4">
        <i class="fa fa-play-circle"></i> Continuer le profil
    </a>
    @php($bgColor="success")
@else

@endif
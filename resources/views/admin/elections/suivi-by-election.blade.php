<div class="row">
    <div class="col-lg-12 col-md-6 col-sm-12">
        <div class="widget">
            <div class="widget-header text-center">
                @if($tabElections->etat == 1)
                    <h5 class="text-green"><i class="fa fa-refresh fa-spin"></i></h5>
                @elseif($tabElections->etat == 0)
                    <h5 class="text-red"><i class="fas fa-toggle-off"></i></h5>
                @elseif($tabElections->etat == 3)
                    <h5 class="text-primary"><i class="fas fa-vote-yea"></i></h5>
                @endif
                
            </div>
            <div class="widget-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="state">
                        <h5 class="mb-30 fw-700">{{$nbrVotant}} votant(s) / {{$nbrElecteur}}</h5>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
                <h5 class="mt-10 d-block text-green ml-10">

                 {{$pResultat}} %
                </h5>
            </div>
            <div class="progress progress-sm">
                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="{{$nbrElecteur}}" style="width: 30%;"></div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
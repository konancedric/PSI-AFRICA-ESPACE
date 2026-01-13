@extends('layouts.main')
@section('title', 'Configuration Web')
@section('content')
    <!-- push external head elements to head -->
    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-ervicems-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <div class="d-inline">
                            <h5>{{ __('Configuration Web')}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-ervicem">
                                <a href="{{url('dashboard')}}"><i class="fa fa-tag"></i></a>
                            </li>
                            <li class="breadcrumb-ervicem">
                                <a href="#">{{ __('Configuration Web')}}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-header bg-dark text-white text-center">
                        PARAMETRER LES INFORMATIONS WEB
                    </div>
                    <div class="card-body">
                        <form class="form-horizontal row" method="POST" action="{{url('configuration')}}" enctype="multipart/form-data" >
                            @csrf
                            <input type="hidden" name="user1d" value="{{Auth::user()->id}}">
                            <input type="hidden" name="id" value="{{$dataConfigWeb->id}}">
                            <input type="hidden" name="last_logo_ent" value="{{$dataConfigWeb->logo_ent}}">
                            <div class="form-group col-md-6">
                                <label for="denomination"><i class="fa fa-tag"></i> {{ __('Denomination')}} <span class="text-red">*</span></label>
                                <input type="text" placeholder="{{$dataConfigWeb->denomination}}" value="{{$dataConfigWeb->denomination}}" class="form-control" name="denomination" id="denomination" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email"><i class="fa fa-envelope"></i> {{ __('Email')}} <span class="text-red">*</span></label>
                                <input type="email"  placeholder="{{$dataConfigWeb->email}}" value="{{$dataConfigWeb->email}}" class="form-control" name="email" id="email" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="adresse"><i class="fa fa-map"></i> {{ __('Adresse')}} <span class="text-red">*</span></label>
                                <input type="text" placeholder="{{$dataConfigWeb->adresse}}" value="{{$dataConfigWeb->adresse}}" class="form-control" name="adresse" id="adresse" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contact"><i class="fa fa-phone"></i> {{ __('Contact')}} <span class="text-red">*</span></label>
                                <input type="text" placeholder="{{$dataConfigWeb->contact}}" value="{{$dataConfigWeb->contact}}" class="form-control" name="contact" id="contact" required />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_video"><i class="fa fa-video"></i> {{ __('Lien Vidéo')}}</label>
                                <input type="text" placeholder="{{$dataConfigWeb->link_video}}" value="{{$dataConfigWeb->link_video}}" class="form-control" name="link_video" id="link_video" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_facebook"><i class="fa fa-facebook"></i> {{ __('Lien Facebook')}}</label>
                                <input type="text" placeholder="{{$dataConfigWeb->link_facebook}}" value="{{$dataConfigWeb->link_facebook}}" class="form-control" name="link_facebook" id="link_facebook" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_linkedin"><i class="fa fa-linkedin"></i> {{ __('Lien Linkedin')}}</label>
                                <input type="text" placeholder="{{$dataConfigWeb->link_linkedin}}" value="{{$dataConfigWeb->link_linkedin}}" class="form-control" name="link_linkedin" id="link_linkedin" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_twitter"><i class="fa fa-twitter"></i> {{ __('Lien Twitter')}}</label>
                                <input type="text" placeholder="{{$dataConfigWeb->link_twitter}}" value="{{$dataConfigWeb->link_twitter}}" class="form-control" name="link_twitter" id="link_twitter" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="link_instagram"><i class="fa fa-instagram"></i> {{ __('Lien Instagram')}}</label>
                                <input type="text" placeholder="{{$dataConfigWeb->link_instagram}}" value="{{$dataConfigWeb->link_instagram}}" class="form-control" name="link_instagram" id="link_instagram" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="num_whatsapp"><i class="fa fa-whatsapp"></i> {{ __('Numéro WhatsApp')}}</label>
                                <input type="text" placeholder="{{$dataConfigWeb->num_whatsapp}}" value="{{$dataConfigWeb->num_whatsapp}}" class="form-control" name="num_whatsapp" id="num_whatsapp" />
                            </div>
                            <div class="form-group col-md-6">
                                <label for="logo_ent"><i class="fa fa-file"></i> {{ __('Logo')}}</label>
                                <input type="file" class="form-control" id="logo_ent" accept="image/*" name="logo_ent" placeholder="logo">
                                @error('logo_ent')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @if($dataConfigWeb->logo_ent != "")
                                    <img src="/upload/config_web/{{$dataConfigWeb->logo_ent}}" class="mt-2 w-100" align="center"/>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="img_pub"><i class="fa fa-file"></i> {{ __('Image Pub')}}</label>
                                <input type="file" class="form-control" id="img_pub" accept="image/*" name="img_pub" placeholder="logo">
                                @error('img_pub')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @if($dataConfigWeb->img_pub != "")
                                    <img src="/upload/config_web/{{$dataConfigWeb->img_pub}}" class="mt-2 w-100" align="center"/>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description"><i class="fa fa-comments"></i> {{ __('Description')}}</label>
                                <textarea name="description" name="description" rows="5" class="form-control" placeholder="{{$dataConfigWeb->description}}" >{{$dataConfigWeb->description}}</textarea>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group"><br/>
                                    <button type="submit" class="btn btn-primary btn-rounded"><i class="fa fa-check-circle"></i> {{ __('Mettre à jour')}}</button>
                                    <button type="reset" class="btn btn-dark btn-rounded">{{ __('Annuler')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')

    @endpush
@endsection

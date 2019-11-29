@extends('layouts.app')

@section('title', 'Widget Overview')

@section('content')
<div id="widget-page" class="">
        
    <div class="row m-t-20 p-b-10 section-border-bottom">
        <div class="col-md-12">
            <h3 class="page-title m-t-0 color-dark">Widget Overview</h3>
        </div>
    </div>
    <div class="row p-t-25 m-b-20">
        <div class="col-md-5 col-12">
            <h5 class="bolder m-b-15">Design</h5>
            <p class="m-b-0">Customize the look and feel of how your On-Site Widget is presented to customers.</p>
        </div>
        <div class="col-md-7 col-12">
            <div class="well bg-white p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-display m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5">
                                <span class="bold">Tab</span>
                                <span class="d-block">Customize how the tab looks on your site.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.widget.tab') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-display m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5">
                                <span class="bold">Widget</span>
                                <span class="d-block">Customize all screens related to your program.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.widget.edit-not-logged-in') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            <div class="well bg-white m-t-15 p-t-20 p-b-20 p-l-20 p-r-20">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group m-b-0">
                            <i class="icon-display f-s-26 m-r-10 v-a-t m-t-15"></i> 
                            <label class="m-b-0 m-t-5">
                                <span class="bold">Branding</span>
                                <span class="d-block">Set your main color and font styles for your widget.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-right m-t-5">
                        <a class="bold color-blue f-s-15" href="{{ route('display.widget.branding') }}">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('spark::layouts.app')

@include('spark::kiosk.resource._scripts')

@section('content')
    <div class="spark-screen container">
        <div class="row">
            <!-- Tabs -->
            <div class="col-md-3 spark-settings-tabs">
                <aside>

                    @include('spark::_partials._kiosk-nav', ['disableTab' => true])

                </aside>
            </div>

            <!-- Tab cards -->
            <div class="col-md-9">
                <div class="tab-content">
                    <div class="tab-pane active">

                        <div class="card card-default">
                            <div class="card-body px-0 px-0">

                                <form action="{{ route('spark.kiosk.resources.case-studies.store') }}" method="post" enctype="multipart/form-data">

                                    @csrf

                                    <div class="w-100 pb-3">
                                        <div class="row">
                                            <div class="col-6 text-left">
                                                <h4 class="mb-0 ml-3">
                                                    {{__('Articles')}} - {{__('Add New')}}
                                                </h4>
                                            </div>
                                            <div class="col-6 text-right">
                                                <button type="submit" class="btn btn-primary mr-3">
                                                    {{__('Save')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-100 border-bottom"></div>
                                    <div class="px-3 pt-3">

                                        @if (session()->has('alert.success'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session()->get('alert.success') }}
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger" role="alert">
                                                <p>{{ __('Errors:') }}</p>
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Content')}}</p>
                                            <p>{{__('This tool will allow you to post new articles into the Lootly website.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label for="inputTitle" class="font-weight-bold">
                                                    {{__('Title')}}
                                                </label>

                                                <input type="text" id="inputTitle" name="title" value="{{ old('title') }}" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('title'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('title') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="selectCategory" class="font-weight-bold">
                                                    {{__('Category')}}
                                                </label>

                                                <select onchange="onChangeCategoryCreate()" name="category_id" class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" id="selectCategory">

                                                    <option value=""></option>

                                                    @foreach ($categories as $value)
                                                        <option value="{{ $value->id }}" {{ $value->id == old('category_id', $categoryId) ? 'selected' : '' }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach;
                                                </select>

                                                @if ($errors->has('category_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('category_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputIndustry" class="font-weight-bold">
                                                    {{__('Industry')}}
                                                </label>

                                                <input type="text" id="inputIndustry" name="case_studies[industry]" value="{{ old('case_studies.industry') }}" class="form-control {{ $errors->has('case_studies.industry') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.industry'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.industry') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputPlatform" class="font-weight-bold">
                                                    {{__('Platform')}}
                                                </label>

                                                <input type="text" id="inputPlatform" name="case_studies[platform]" value="{{ old('case_studies.platform') }}" class="form-control {{ $errors->has('case_studies.platform') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.platform'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.platform') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputFavoriteFeature" class="font-weight-bold">
                                                    {{__('Favorite Feature')}}
                                                </label>

                                                <input type="text" id="inputFavoriteFeature" name="case_studies[favorite_feature]" value="{{ old('case_studies.favorite_feature') }}" class="form-control {{ $errors->has('case_studies.favorite_feature') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.favorite_feature'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.favorite_feature') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputStatFirstTitle" class="font-weight-bold">
                                                    {{__('Stat 1 Title')}}
                                                </label>

                                                <input type="text" id="inputStatFirstTitle" name="case_studies[stat_first_title]" value="{{ old('case_studies.stat_first_title') }}" class="form-control {{ $errors->has('case_studies.stat_first_title') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.stat_first_title'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.stat_first_title') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputStatFirstValue" class="font-weight-bold">
                                                    {{__('Stat 1 Value')}}
                                                </label>

                                                <input type="text" id="inputStatFirstValue" name="case_studies[stat_first_value]" value="{{ old('case_studies.stat_first_value') }}" class="form-control {{ $errors->has('case_studies.stat_first_value') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.stat_first_value'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.stat_first_value') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputStatSecondTitle" class="font-weight-bold">
                                                    {{__('Stat 2 Title')}}
                                                </label>

                                                <input type="text" id="inputStatSecondTitle" name="case_studies[stat_second_title]" value="{{ old('case_studies.stat_second_title') }}" class="form-control {{ $errors->has('case_studies.stat_second_title') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.stat_second_title'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.stat_second_title') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputStatSecondValue" class="font-weight-bold">
                                                    {{__('Stat 2 Value')}}
                                                </label>

                                                <input type="text" id="inputStatSecondValue" name="case_studies[stat_second_value]" value="{{ old('case_studies.stat_second_value') }}" class="form-control {{ $errors->has('case_studies.stat_second_value') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.stat_second_value'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.stat_second_value') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputStatThirdTitle" class="font-weight-bold">
                                                    {{__('Stat 3 Title')}}
                                                </label>

                                                <input type="text" id="inputStatThirdTitle" name="case_studies[stat_third_title]" value="{{ old('case_studies.stat_third_title') }}" class="form-control {{ $errors->has('case_studies.stat_third_title') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.stat_third_title'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.stat_third_title') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputStatThirdValue" class="font-weight-bold">
                                                    {{__('Stat 3 Value')}}
                                                </label>

                                                <input type="text" id="inputStatThirdValue" name="case_studies[stat_third_value]" value="{{ old('case_studies.stat_third_value') }}" class="form-control {{ $errors->has('case_studies.stat_third_value') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.stat_third_value'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.stat_third_value') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputTopQuote" class="font-weight-bold">
                                                    {{__('Top Quote')}}
                                                </label>

                                                <summer-note
                                                        id="inputTopQuote"
                                                        name="case_studies[top_quote]"
                                                        value="{{ old('case_studies.top_quote') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.top_quote'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.top_quote') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="inputCustomerName" class="font-weight-bold">
                                                    {{__('Customer Name')}}
                                                </label>

                                                <input type="text" id="inputCustomerName" name="case_studies[customer_name]" value="{{ old('case_studies.customer_name') }}" class="form-control {{ $errors->has('case_studies.customer_name') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.customer_name'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.customer_name') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputPositionTitle" class="font-weight-bold">
                                                    {{__('Position / Title')}}
                                                </label>

                                                <input type="text" id="inputPositionTitle" name="case_studies[position_title]" value="{{ old('case_studies.position_title') }}" class="form-control {{ $errors->has('case_studies.position_title') ? 'is-invalid' : '' }}">

                                                @if ($errors->has('case_studies.position_title'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.position_title') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Company Overview')}}</p>
                                            <p>{{__('Write a short description about the company and provide an image of the website.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputCompanyBody" class="font-weight-bold">
                                                    {{__('Body')}}
                                                </label>

                                                <summer-note
                                                        id="inputCompanyBody"
                                                        name="case_studies[company_body]"
                                                        value="{{ old('case_studies.company_body') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.company_body'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.company_body') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputCompanyImage" class="font-weight-bold">
                                                    {{__('Company Overview Image (max: 550px 360px)')}}
                                                </label>

                                                <input type="file" id="inputCompanyImage" name="case_studies[company_image]" class="form-control-file">

                                                @if ($errors->has('case_studies.company_image'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.company_image') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Challenge')}}</p>
                                            <p>{{__('Write a short description about the challenges that this company faced and why finding a loyalty program was important.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputChallengeBody" class="font-weight-bold">
                                                    {{__('Body')}}
                                                </label>

                                                <summer-note
                                                        id="inputChallengeBody"
                                                        name="case_studies[challenge_body]"
                                                        value="{{ old('case_studies.challenge_body') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.challenge_body'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.challenge_body') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputChallengeQuote" class="font-weight-bold">
                                                    {{__('Quote')}}
                                                </label>

                                                <summer-note
                                                        id="inputChallengeQuote"
                                                        name="case_studies[challenge_quote]"
                                                        value="{{ old('case_studies.challenge_quote') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.challenge_quote'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.challenge_quote') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Solution')}}</p>
                                            <p>{{__('Write a short description about finding Lootly, their goals, and what tools they used from Lootly / why they used those.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputSolutionBody" class="font-weight-bold">
                                                    {{__('Body')}}
                                                </label>

                                                <summer-note
                                                        id="inputSolutionBody"
                                                        name="case_studies[solution_body]"
                                                        value="{{ old('case_studies.solution_body') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.solution_body'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.solution_body') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputSolutionQuote" class="font-weight-bold">
                                                    {{__('Quote')}}
                                                </label>

                                                <summer-note
                                                        id="inputSolutionQuote"
                                                        name="case_studies[solution_quote]"
                                                        value="{{ old('case_studies.solution_quote') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.solution_quote'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.solution_quote') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputSolutionImage" class="font-weight-bold">
                                                    {{__('Solution Image (max: 550px 360px)')}}
                                                </label>

                                                <input type="file" id="inputSolutionImage" name="case_studies[solution_image]" class="form-control-file">

                                                @if ($errors->has('case_studies.solution_image'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.solution_image') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Results')}}</p>
                                            <p>{{__('Write a short description about how Lootly ultimately helped the business. Provide data points and numbers to show the results better.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputResultsBody" class="font-weight-bold">
                                                    {{__('Body')}}
                                                </label>

                                                <summer-note
                                                        id="inputResultsBody"
                                                        name="case_studies[results_body]"
                                                        value="{{ old('case_studies.results_body') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('case_studies.results_body'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.results_body') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputResultsImage" class="font-weight-bold">
                                                    {{__('Results Image (max: 550px 360px)')}}
                                                </label>

                                                <input type="file" id="inputResultsImage" name="case_studies[results_image]" class="form-control-file">

                                                @if ($errors->has('case_studies.results_image'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('case_studies.results_image') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Additional information')}}</p>
                                            <p>{{__('Customize the meta description, mini description and image for the resource center page.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputMetaDescription" class="font-weight-bold">
                                                    {{__('Meta Description')}}
                                                </label>

                                                <textarea name="meta_description" class="form-control {{ $errors->has('meta_description') ? 'is-invalid' : '' }}" id="inputMetaDescription" rows="5">{{ old('meta_description') }}</textarea>

                                                @if ($errors->has('meta_description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('meta_description') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputDescription" class="font-weight-bold">
                                                    {{__('Mini Description')}}
                                                </label>

                                                <summer-note
                                                        id="inputDescription"
                                                        name="description"
                                                        value="{{ old('description') }}"
                                                        height="200"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('description') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="inputMiniImage" class="font-weight-bold">
                                                    {{__('Mini Image (max: 364px x 150px)')}}
                                                </label>

                                                <input type="file" id="inputMiniImage" name="mini_image" class="form-control-file">

                                                @if ($errors->has('mini_image'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mini_image') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-4">

                                                <label for="selectStatus" class="font-weight-bold">
                                                    {{__('Status')}}
                                                </label>

                                                <select name="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" id="selectStatus">
                                                    @foreach ($statuses as $id => $name)
                                                        <option value="{{ $id }}" {{ $id == old('status') ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach;
                                                </select>

                                                @if ($errors->has('status'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('status') }}
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

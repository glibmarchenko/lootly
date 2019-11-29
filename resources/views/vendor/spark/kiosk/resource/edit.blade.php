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

                                <form action="{{ route('spark.kiosk.resources.update', ['id' => $resource->id]) }}" method="post" enctype="multipart/form-data">

                                    @csrf
                                    @method('PUT')

                                    <div class="w-100 pb-3">
                                        <div class="row">
                                            <div class="col-6 text-left">
                                                <h4 class="mb-0 ml-3">
                                                    {{__('Articles')}} - {{__('Edit')}}
                                                </h4>
                                            </div>
                                            <div class="col-6 text-right">
                                                <button type="submit" class="btn btn-primary mr-3">
                                                    {{__('Update')}}
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

                                                <input type="text" id="inputTitle" name="title" value="{{ old('title', $resource->title) }}" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}">

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

                                                <select onchange="onChangeCategoryEdit()" name="category_id" class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" id="selectCategory">

                                                    <option value=""></option>

                                                    @foreach ($categories as $value)
                                                        <option value="{{ $value->id }}" {{ $value->id == old('category_id', $resource->category_id) ? 'selected' : '' }}>
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
                                            <div class="form-group col-12">
                                                <label for="inputBody" class="font-weight-bold">
                                                    {{__('Body')}}
                                                </label>

                                                <summer-note
                                                        id="inputBody"
                                                        name="body"
                                                        value="{{ old('body', $resource->body) }}"
                                                        height="400"
                                                        class="form-control"
                                                ></summer-note>

                                                @if ($errors->has('body'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('body') }}
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
                                                        value="{{ old('description', $resource->description) }}"
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

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Images')}}</p>
                                            <p>{{__('Add an image for the resource center and the main image of the article.')}}</p>
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

                                                @if ($resource->mini_image)
                                                    <div class="mt-3">
                                                        <a href="{{ asset('storage/' . $resource->mini_image) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $resource->mini_image) }}" class="img-thumbnail" alt="">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputFeaturedImage" class="font-weight-bold">
                                                    {{__('Featured Image (max: 1000px x 650px)')}}
                                                </label>

                                                <input type="file" id="inputFeaturedImage" name="featured_image" class="form-control-file">

                                                @if ($errors->has('featured_image'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('featured_image') }}
                                                    </div>
                                                @endif

                                                @if ($resource->featured_image)
                                                    <div class="mt-3">
                                                        <a href="{{ asset('storage/' . $resource->featured_image) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $resource->featured_image) }}" class="img-thumbnail" alt="">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-100 my-3 border-bottom"></div>

                                        <div class="mb-3">
                                            <p class="font-weight-bold">{{__('Additional information')}}</p>
                                            <p>{{__('Customize the meta tags associated with this article, along with the author information.')}}</p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-12">
                                                <label for="inputMetaDescription" class="font-weight-bold">
                                                    {{__('Meta Description')}}
                                                </label>

                                                <textarea name="meta_description" class="form-control {{ $errors->has('meta_description') ? 'is-invalid' : '' }}" id="inputMetaDescription" rows="5">{{ old('meta_description', $resource->meta_description) }}</textarea>

                                                @if ($errors->has('meta_description'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('meta_description') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label for="selectAuthor" class="font-weight-bold">
                                                    {{__('Author')}}
                                                </label>

                                                <select name="author_id" class="form-control {{ $errors->has('author_id') ? 'is-invalid' : '' }}" id="selectAuthor">
                                                    @foreach ($authors as $value)
                                                        <option value="{{ $value->id }}" {{ $value->id == old('author_id', $resource->author_id) ? 'selected' : '' }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach;
                                                </select>

                                                @if ($errors->has('author_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('author_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-4">

                                                <label for="selectStatus" class="font-weight-bold">
                                                    {{__('Status')}}
                                                </label>

                                                <select name="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" id="selectStatus">
                                                    @foreach ($statuses as $id => $name)
                                                        <option value="{{ $id }}" {{ $id == old('status', $resource->status) ? 'selected' : '' }}>
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

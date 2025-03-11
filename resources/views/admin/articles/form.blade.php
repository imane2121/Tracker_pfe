@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1>{{ isset($article) ? 'Edit Article' : 'Create Article' }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($article))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $article->title ?? '') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        @foreach(['news', 'educational', 'awareness', 'best_practices', 'initiative', 'report', 'event'] as $category)
                            <option value="{{ $category }}" {{ (old('category', $article->category ?? '') == $category) ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                              rows="10" required>{{ old('content', $article->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($article) && $article->image)
                        <div class="mt-2">
                            <img src="{{ Storage::url($article->image) }}" alt="Current image" style="max-height: 200px">
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_featured" class="form-check-input" 
                               value="1" {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured Article</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($article) ? 'Update' : 'Create' }} Article
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
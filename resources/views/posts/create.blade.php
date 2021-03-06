@extends('app')

@section('title', '投稿する | VS-Connect')

@section('content')
@include('nav')
<div class="card-wrapper">
  <div class="container">
    <div class="card">
      @include('error_list')
      <form method="POST" action="{{ route('posts.store') }}">
        @csrf
        <div class="md-form">
          <p class="name-label">募集タイトル<span class="form-alert">※</span></p>
          <input type="text" name="title" class="form-control p-0" value="{{ $post->title ?? old('title') }}" required>
        </div>

        <div class="form-group">
          <p class="name-label">投稿主の機体コストを選択してください<span class="form-alert">※</span></p>
          <select name="category_id" class="form-control form-cost">
            @foreach($categories as $id => $name)
            <option value="{{ $id }}">{{ $name }} cost</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <p class="name-label">投稿主の機体名を登録してください</p>
          <post-tags-input
          :initial-tags='@json($tagNames ?? [])'
          :autocomplete-items='@json($allTagNames ?? [])'>
          </post-tags-input>
        </div>

        <div class="form-group">
          <p class="name-label">募集詳細<span class="form-alert">※</span></p>
          <textarea
            name="body"
            class="form-control"
            rows="6"
            placeholder="ここに詳細内容書いてください。"
            required>{{ $post->body ?? old('body') }}
          </textarea>
        </div>
        <div class="form-btn">
          <a href="{{ route('posts.index') }}" class="btn btn-back">戻る</a>
          <button type="submit" class="btn btn-submit"><i class="fas fa-paper-plane"></i> 投稿</button>
        </div>
      </form>
    </div>
  </div>
</div>
@include('footer')
@endsection

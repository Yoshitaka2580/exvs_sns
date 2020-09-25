<div class="card">
  <div class="card-body">
    <a href="{{ route('users.show', ['name' => $post->user->name]) }}" class= "card-user">
    @if(!empty($post->user->thumbnail))
        <img src="/storage/user/{{ $post->user->thumbnail }}" class="editThumbnail">
        @else
        <i class="fas fa-user-circle fa-3x"></i>
    @endif
    </a>
    <h5 class="ml-3">
      <a href="{{ route('users.show', ['name' => $post->user->name]) }}" class="card-user">
        {{ $post->user->name }}
      </a>
      <p class="card-created">{{ $post->created_at->format('Y/m/d H:i') }}</p>
    </h5>

  @if( Auth::id() === $post->user_id )
    <!-- ドロップダウンメニュー -->
      <div class="ml-auto card-text">
        <div class="dropdown">
          <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-check"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('posts.edit', ['post' => $post]) }}">
              <i class="fas fa-pen mr-1"></i>記事を更新する
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" data-toggle="modal" data-target="#modal-delete-{{ $post->id }}">
              <i class="fas fa-trash-alt mr-1"></i>記事を削除する
            </a>
          </div>
        </div>
      </div>

      <!-- モーダル -->
      <div id="modal-delete-{{ $post->id }}" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="POST" action="{{ route('posts.destroy', ['post' => $post]) }}">
              @csrf
              @method('DELETE')
              <div class="modal-body">
                {{ $post->title }}を削除します。よろしいですか？
              </div>
              <div class="modal-footer justify-content-between">
                <a class="btn btn-outline-grey" data-dismiss="modal">キャンセル</a>
                <button type="submit" class="btn btn-danger">削除する</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif
  </div>

  @foreach($post->tags as $tag)
    @if($loop->first)
    <div class="card-body tag-body">
      <div class="tag-text">
    @endif
        <a href="{{ route('tags.show', ['name' => $tag->name]) }}" class="btn-backred tag-hashtag">
          {{ $tag->hashtag }}
        </a>
    @if($loop->last)
      </div>
    </div>
    @endif
  @endforeach
  
  <div class="text-body">
    <h4 class="card-title card-text">{{ $post->title }}</h4>
    <a href="{{ route('posts.show', ['post' => $post]) }}">
      詳細を見る
    </a>
  </div>
  <div class="card-body">
    <post-like
      :initial-is-liked-by='@json($post->isLikedBy(Auth::user()))'
      :initial-count-likes='@json($post->count_likes)'
      :authorized='@json(Auth::check())'
      endpoint="{{ route('posts.like', ['post' => $post]) }}"
    >
    </post-like>
    <button class="btn btn-like-comment" onclick="location.href='{{ route('posts.show', ['post' => $post]) }}'">
      <i class="fas fa-comment-dots"></i> {{ $post->comments->count() }}
    </button>
  </div>
</div>

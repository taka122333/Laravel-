<?php
■ルート■
//web.php
Route::get('post', [PostController::class, 'index'])
->name('post.index');
Route::get('post/create', [PostController::class, 'create'])
->name('post.create');
Route::post('post', [PostController::class, 'store'])
->name('post.store');
Route::get('post/show/{post}', [PostController::class, 'show'])
->name('post.show');
Route::get('post/{post}/edit', [PostController::class, 'edit'])
->name('post.edit');
Route::patch('post/{post}', [PostController::class, 'update'])
->name('post.update');
Route::delete('post/{post}', [PostController::class, 'destroy'])
->name('post.destroy');

//7つをまとめて
Route::resource('post', PostController::class);

■コントローラー■
//PostController.php
public function index() {
    // $posts = Post::all();
    $posts = Post::paginate(10);
    return view('post.index', compact('posts'));
}

public function create() {
    return view('post.create');
}

public function store(Request $request) {
    $validated = $request->validate([
        'title' => 'required|max:20',
        'body' => 'required|max:400',
    ]);
    $validated['user_id'] = auth()->id();
    $post = Post::create($validated);
    $request->session()->flash('message', '保存しました');
    return redirect()->route('post.create');
}

public function show(Post $post) {
    return view('post.show', compact('post'));
}

public function edit(Post $post) {
    return view('post.edit', compact('post'));
}

public function update(Request $request, Post $post) {
    $validated = $request->validate([
        'title' => 'required|max:20',
        'body' => 'required|max:400',
    ]);
    $validated['user_id'] = auth()->id();
    $post->update($validated);
    $request->session()->flash('message', '更新しました');
    return redirect()->route('post.show', compact('post'));
}

public function destroy(Request $request, Post $post) {
    $post->delete();
    $request->session()->flash('message', '削除しました');
    return redirect()->route('post.index');
}

?>

■ビュー■
<!-- index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            一覧表示
        </h2>
    </x-slot>
    @if (session('message'))
        <div class="text-red-600 font-bold">
            {{ session('message') }}
        </div>
    @endif
    @foreach ($posts as $post)
        <h1>
            件名:
            <a href="{{ route('post.show', $post) }}">
                {{ $post->title }}
            </a>
        </h1>
        <hr>
        <p>
            {{ $post->body }}
        </p>
        <p>
            {{ $post->created_at }}
        </p>
    @endforeach
    <div class="mb-4">
        {{ $posts->links() }}
    </div>
</x-app-layout>

<!-- create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            フォーム
        </h2>
    </x-slot>
    @if (session('message'))
        <div class="text-red-600 font-bold">
            {{ session('message') }}
        </div>
    @endif
    <form method="post" action="{{ route('post.store') }}">
        @csrf
        <div>
            <label for="title">件名</label>
            <x-input-error :messages="$errors->get('title')" />
            <input type="text" name="title" value="{{ old('title') }}">
        </div>
        <div>
            <label for="body">本文</label>
            <x-input-error :messages="$errors->get('body')" />
            <textarea name="body" id="" cols="30" rows="10">{{ old('body') }}</textarea>
        </div>
        <x-primary-button>
            送信する
        </x-primary-button>
    </form>
</x-app-layout>

<!-- show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            個別表示
        </h2>
    </x-slot>
    @if (session('message'))
        <div class="text-red-600 font-bold">
            {{ session('message') }}
        </div>
    @endif
    <h1>
        {{ $post->title }}
    </h1>
    <hr>
    <p>
        {{ $post->body }}
    </p>
    <p>
        {{ $post->created_at }}
    </p>
    <a href="{{ route('post.edit', $post) }}">
        <x-primary-button>
            編集
        </x-primary-button>
    </a>
    <form method="post" action="{{ route('post.destroy', $post) }}">
        @csrf
        @method('delete')
        <x-primary-button>
            削除
        </x-primary-button>
    </form>
</x-app-layout>

<!-- edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            フォーム
        </h2>
    </x-slot>
    <form method="post" action="{{ route('post.update', $post) }}">
        @csrf
        @method('patch')
        <div>
            <label for="title">件名</label>
            <x-input-error :messages="$errors->get('title')" />
            <input type="text" name="title" value="{{ old('title', $post->title) }}">
        </div>
        <div>
            <label for="body">本文</label>
            <x-input-error :messages="$errors->get('body')" />
            <textarea name="body" id="" cols="30" rows="10">{{ old('body', $post->body) }}</textarea>
        </div>
        <x-primary-button>
            送信する
        </x-primary-button>
    </form>
</x-app-layout>
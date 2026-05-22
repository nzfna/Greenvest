@extends('layouts.admin')
@section('title', 'Sunting Artikel')

@section('content')
<div x-data="articleEditor({{ json_encode($article->content) }})">

<div style="font-size:0.8125rem;color:#9CA3AF;margin-bottom:0.75rem;">
    <a href="{{ route('admin.articles.index') }}" style="color:#6B7280;text-decoration:none;">KELOLA ARTIKEL</a>
    <span style="margin:0 0.375rem;">›</span>
    <span style="color:#111827;font-weight:600;">SUNTING ARTIKEL</span>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
    <h1 class="page-title">Sunting Artikel</h1>
    <div style="display:flex;gap:0.625rem;">
        <a href="{{ route('admin.articles.index') }}"
           style="padding:0.6875rem 1.125rem;background:#F3F4F6;color:#374151;font-size:0.875rem;font-weight:600;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:0.375rem;">
            <i class="ph ph-x"></i> Batal
        </a>
        <button type="submit" form="article-form"
                style="padding:0.6875rem 1.25rem;background:#8B6B1B;color:#fff;font-size:0.875rem;font-weight:600;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;gap:0.375rem;">
            <i class="ph ph-floppy-disk"></i> Simpan Perubahan
        </button>
    </div>
</div>

<form id="article-form" method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="editor-layout">
        <div class="editor-main">
            {{-- Title --}}
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.6875rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#8B6B1B;margin-bottom:0.5rem;">JUDUL ARTIKEL</label>
                <input type="text" name="title"
                       value="{{ old('title', $article->title) }}"
                       style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:1rem;font-weight:600;color:#111827;outline:none;">
                @error('title')<p style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.6875rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#8B6B1B;margin-bottom:0.5rem;">DESKRIPSI SINGKAT</label>
                <textarea name="description" rows="2"
                          style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;color:#374151;outline:none;resize:vertical;font-family:inherit;">{{ old('description', $article->description) }}</textarea>
            </div>

            {{-- Rich Editor --}}
            <div x-ref="editorWrap">
                <div class="rich-toolbar">
                    <button type="button" class="rich-btn" @click="execCmd('bold')"><b>B</b></button>
                    <button type="button" class="rich-btn" @click="execCmd('italic')"><i>I</i></button>
                    <button type="button" class="rich-btn" @click="execCmd('underline')"><u>U</u></button>
                    <div class="rich-sep"></div>
                    <button type="button" class="rich-btn" @click="execCmd('insertUnorderedList')"><i class="ph ph-list-bullets"></i></button>
                    <button type="button" class="rich-btn" @click="execCmd('insertOrderedList')"><i class="ph ph-list-numbers"></i></button>
                    <div class="rich-sep"></div>
                    <button type="button" class="rich-btn" @click="insertLink()"><i class="ph ph-link"></i></button>
                    <button type="button" class="rich-btn" @click="insertImage()"><i class="ph ph-image"></i></button>
                    <div class="rich-sep"></div>
                    <button type="button" class="rich-btn" @click="execCmd('formatBlock','blockquote')"><i class="ph ph-quotes"></i></button>
                    <button type="button" class="rich-btn" style="margin-left:auto;" @click="toggleFullscreen()"><i class="ph ph-arrows-out-simple"></i></button>
                </div>
                <div class="rich-editor" contenteditable="true" x-ref="editor"
                     @input="syncContent()"
                     style="min-height:380px;"></div>
            </div>
            <input type="hidden" name="content" x-bind:value="content">
        </div>

        {{-- Sidebar --}}
        <div class="editor-sidebar">
            <div class="editor-panel">
                <div class="editor-panel-title">KATEGORI</div>
                <select name="category_id" style="width:100%;padding:0.5625rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;appearance:none;">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="editor-panel">
                <div class="editor-panel-title">STATUS</div>
                <select name="status" style="width:100%;padding:0.5625rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;appearance:none;">
                    <option value="published" {{ old('status',$article->status) === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft"     {{ old('status',$article->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="editor-panel">
                <div class="editor-panel-title">RIWAYAT PERUBAHAN</div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.875rem;color:#6B7280;">Total: {{ $article->revision_count }} Revisi</span>
                </div>
            </div>

            {{-- Cover Image --}}
            <div class="editor-panel">
                <div class="editor-panel-title">GAMBAR SAMPUL</div>
                <div x-data="{ preview: '{{ $article->cover_image ? asset('storage/'.$article->cover_image) : '' }}' }">
                    <div style="border:2px dashed #E5E7EB;border-radius:10px;overflow:hidden;cursor:pointer;margin-bottom:0.75rem;"
                         @click="$refs.coverInput.click()">
                        <template x-if="preview">
                            <img :src="preview" style="width:100%;height:120px;object-fit:cover;">
                        </template>
                        <template x-if="!preview">
                            <div style="padding:2rem;text-align:center;color:#9CA3AF;">
                                <i class="ph ph-image" style="font-size:1.75rem;display:block;"></i>
                            </div>
                        </template>
                    </div>
                    <button type="button"
                            style="width:100%;padding:0.5rem;background:#F9FAFB;border:1.5px dashed #E5E7EB;border-radius:8px;font-size:0.8125rem;color:#6B7280;cursor:pointer;"
                            @click="$refs.coverInput.click()">
                        Ganti Gambar Sampul
                    </button>
                    <input type="file" name="cover_image" accept="image/*" x-ref="coverInput" class="sr-only"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                </div>
            </div>

            {{-- Revision history --}}
            <div class="editor-panel">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <div class="editor-panel-title" style="margin-bottom:0;">RIWAYAT PERUBAHAN</div>
                    <i class="ph ph-clock-counter-clockwise" style="color:#9CA3AF;"></i>
                </div>
                @if($article->revision_count === 0)
                    <div style="text-align:center;padding:1rem 0;color:#9CA3AF;">
                        <i class="ph ph-calendar-blank" style="font-size:1.75rem;display:block;margin-bottom:0.5rem;opacity:0.5;"></i>
                        <div style="font-size:0.8125rem;">Belum ada riwayat perubahan</div>
                    </div>
                @else
                    <div style="font-size:0.8125rem;color:#6B7280;">
                        <div style="display:flex;justify-content:space-between;padding:0.375rem 0;border-bottom:1px solid #F3F4F6;">
                            <span>Revisi terakhir</span>
                            <span style="font-weight:600;">{{ $article->updated_at->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:0.375rem 0;">
                            <span>Oleh</span>
                            <span style="font-weight:600;">{{ $article->author->name }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
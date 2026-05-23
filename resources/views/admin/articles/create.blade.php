@extends('layouts.admin')
@section('title', 'Tambah Artikel')

@section('content')
<div x-data="articleEditor({{ json_encode(old('content', '')) }})">

{{-- Breadcrumb + Header --}}
<div style="font-size:0.8125rem;color:#9CA3AF;margin-bottom:0.75rem;">
    <a href="{{ route('admin.articles.index') }}" style="color:#6B7280;text-decoration:none;">KELOLA ARTIKEL</a>
    <span style="margin:0 0.375rem;">›</span>
    <span style="color:#111827;font-weight:600;">TAMBAH ARTIKEL</span>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
    <h1 class="page-title">Tambah Artikel</h1>
    <div style="display:flex;gap:0.625rem;">
        <a href="{{ route('admin.articles.index') }}"
           style="padding:0.6875rem 1.125rem;background:#F3F4F6;color:#374151;font-size:0.875rem;font-weight:600;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:0.375rem;border:none;cursor:pointer;">
            <i class="ph ph-x"></i> Batal
        </a>
        <button type="submit" form="article-form"
                style="padding:0.6875rem 1.25rem;background:#8B6B1B;color:#fff;font-size:0.875rem;font-weight:600;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;gap:0.375rem;">
            <i class="ph ph-floppy-disk"></i> Simpan Artikel
        </button>
    </div>
</div>

<form id="article-form" method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="editor-layout">
        {{-- Main Editor --}}
        <div class="editor-main">

            {{-- Title --}}
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.6875rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#8B6B1B;margin-bottom:0.5rem;">JUDUL ARTIKEL</label>
                <input type="text" name="title" x-model="title"
                       class="{{ $errors->has('title') ? 'form-input error' : '' }}"
                       placeholder="Masukkan judul yang memikat..."
                       value="{{ old('title') }}"
                       style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid {{ $errors->has('title') ? '#EF4444' : '#E5E7EB' }};border-radius:10px;font-size:1rem;font-weight:600;color:#111827;outline:none;">
                @error('title')
                    <p style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:1.25rem;">
                <label style="display:block;font-size:0.6875rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#8B6B1B;margin-bottom:0.5rem;">DESKRIPSI SINGKAT</label>
                <textarea name="description" rows="2"
                          placeholder="Tuliskan ringkasan artikel untuk pembaca..."
                          style="width:100%;padding:0.6875rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:10px;font-size:0.875rem;color:#374151;outline:none;resize:vertical;font-family:inherit;">{{ old('description') }}</textarea>
            </div>

            {{-- Rich Editor --}}
            <div x-ref="editorWrap">
                <div class="rich-toolbar">
                    <button type="button" class="rich-btn" @click="execCmd('bold')" title="Bold"><b>B</b></button>
                    <button type="button" class="rich-btn" @click="execCmd('italic')" title="Italic"><i>I</i></button>
                    <button type="button" class="rich-btn" @click="execCmd('underline')" title="Underline"><u>U</u></button>
                    <div class="rich-sep"></div>
                    <button type="button" class="rich-btn" @click="execCmd('insertUnorderedList')" title="Bullet list">
                        <i class="ph ph-list-bullets"></i>
                    </button>
                    <button type="button" class="rich-btn" @click="execCmd('insertOrderedList')" title="Numbered list">
                        <i class="ph ph-list-numbers"></i>
                    </button>
                    <div class="rich-sep"></div>
                    <button type="button" class="rich-btn" @click="insertLink()" title="Link">
                        <i class="ph ph-link"></i>
                    </button>
                    <button type="button" class="rich-btn" @click="insertImage()" title="Gambar">
                        <i class="ph ph-image"></i>
                    </button>
                    <div class="rich-sep"></div>
                    <button type="button" class="rich-btn" @click="execCmd('formatBlock', 'blockquote')" title="Blockquote">
                        <i class="ph ph-quotes"></i>
                    </button>
                    <button type="button" class="rich-btn" style="margin-left:auto;" @click="toggleFullscreen()" title="Fullscreen">
                        <i class="ph ph-arrows-out-simple"></i>
                    </button>
                </div>
                <div class="rich-editor" contenteditable="true"
                     x-ref="editor"
                     @input="syncContent()"
                     x-init="$el.innerHTML = content"
                     style="min-height:380px;"></div>
            </div>

            {{-- Hidden content input --}}
            <input type="hidden" name="content" x-bind:value="content">
            @error('content')
                <p style="font-size:0.75rem;color:#EF4444;margin-top:0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sidebar --}}
        <div class="editor-sidebar">
            {{-- Kategori --}}
            <div class="editor-panel">
                <div class="editor-panel-title">KATEGORI</div>
                <select name="category_id"
                        style="width:100%;padding:0.5625rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;appearance:none;">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="editor-panel">
                <div class="editor-panel-title">STATUS</div>
                <select name="status"
                        style="width:100%;padding:0.5625rem 0.875rem;background:#F9FAFB;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;outline:none;cursor:pointer;appearance:none;">
                    <option value="published" {{ old('status','published') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft"     {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            {{-- Revision count (new article = 0) --}}
            <div class="editor-panel">
                <div class="editor-panel-title">RIWAYAT PERUBAHAN</div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:0.875rem;color:#6B7280;">Total: 0 Revisi</span>
                </div>
            </div>

            {{-- Cover Image --}}
            <div class="editor-panel">
                <div class="editor-panel-title">GAMBAR SAMPUL</div>
                <div x-data="{ preview: null }"
                     style="border:2px dashed #E5E7EB;border-radius:10px;padding:1.5rem;text-align:center;cursor:pointer;"
                     @click="$refs.coverInput.click()"
                     @dragover.prevent @drop.prevent="
                        const f = $event.dataTransfer.files[0];
                        if(f) { preview = URL.createObjectURL(f); $refs.coverInput.files = $event.dataTransfer.files; }
                     ">
                    <template x-if="!preview">
                        <div>
                            <i class="ph ph-image" style="font-size:1.75rem;color:#9CA3AF;display:block;margin-bottom:0.5rem;"></i>
                            <div style="font-size:0.8125rem;font-weight:600;color:#374151;margin-bottom:0.25rem;">Pilih Gambar Sampul</div>
                            <div style="font-size:0.75rem;color:#9CA3AF;">Resolusi disarankan 1200 × 800px (Max 2MB)</div>
                        </div>
                    </template>
                    <template x-if="preview">
                        <img :src="preview" style="width:100%;height:120px;object-fit:cover;border-radius:8px;">
                    </template>
                    <input type="file" name="cover_image" accept="image/*" x-ref="coverInput" class="sr-only"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                </div>
                @error('cover_image')
                    <p style="font-size:0.75rem;color:#EF4444;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Revision history (empty for new) --}}
            <div class="editor-panel">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <div class="editor-panel-title" style="margin-bottom:0;">RIWAYAT PERUBAHAN</div>
                    <i class="ph ph-clock-counter-clockwise" style="color:#9CA3AF;"></i>
                </div>
                <div style="text-align:center;padding:1rem 0;color:#9CA3AF;">
                    <i class="ph ph-calendar-blank" style="font-size:1.75rem;display:block;margin-bottom:0.5rem;opacity:0.5;"></i>
                    <div style="font-size:0.8125rem;">Belum ada riwayat perubahan</div>
                    <div style="font-size:0.75rem;margin-top:0.25rem;">Simpan artikel untuk mencatat versi pertama.</div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
@endsection

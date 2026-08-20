@extends('admin.layouts.app')

@section('title', 'Edit Page')

@push('styles')
<style>
.codex-editor { border: 1px solid #d2d6de; border-radius: 4px; }
.codex-editor__redactor { padding-bottom: 50px !important; }
.ce-block__content { max-width: none; padding: 0 20px; }
.ce-toolbar__content { max-width: none; padding: 0 20px; }
.ce-toolbar__actions { padding-right: 0; }

/* Media & Text tool */
.media-text-wrap { display:flex; gap:20px; background:#fff; border:1px solid #e8e8eb; border-radius:8px; padding:20px; }
.media-text-wrap.mt-reverse { flex-direction:row-reverse; }
.media-text-wrap .mt-media { flex:0 0 45%; min-width:160px; }
.media-text-wrap .mt-text { flex:1; min-width:200px; }
.media-text-wrap .mt-label { font-size:11px; color:#888; margin-bottom:4px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
.media-text-wrap .mt-url-input { width:100%; padding:8px; border:1px solid #d2d6de; border-radius:4px; font-size:13px; box-sizing:border-box; }
.media-text-wrap .mt-browse { margin-top:6px; padding:4px 12px; background:#3c8dbc; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px; }
.media-text-wrap .mt-preview { margin-top:10px; min-height:90px; background:#fafafa; border-radius:4px; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.media-text-wrap .mt-preview img { max-width:100%; max-height:160px; object-fit:contain; border-radius:4px; }
.media-text-wrap .mt-title { width:100%; padding:8px; border:1px solid #d2d6de; border-radius:4px; font-size:16px; font-weight:600; box-sizing:border-box; margin-bottom:8px; }
.media-text-wrap .mt-content { width:100%; min-height:100px; padding:8px; border:1px solid #d2d6de; border-radius:4px; font-size:14px; line-height:1.7; box-sizing:border-box; outline:none; background:#fff; }
.media-text-wrap .mt-content:empty:before { content:attr(data-placeholder); color:#aaa; }
.media-text-wrap .mt-error { color:#e74c3c; font-size:12px; text-align:center; }
@media (max-width:640px) { .media-text-wrap, .media-text-wrap.mt-reverse { flex-direction:column!important; } }
</style>
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit: {{ $page->title }}</h3>
                    </div>
                    <form action="{{ route('admin.pages.update', $page) }}" method="POST" id="pageForm">
                        @csrf @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Page Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title', $page->title) }}" required>
                                        @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="slug">Slug</label>
                                        <input type="text" name="slug" id="slug"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            value="{{ old('slug', $page->slug) }}">
                                        <small class="text-muted">Will appear as: /page/{{ old('slug', $page->slug) }}</small>
                                        @error('slug')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Content</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#browseMediaModal">
                                        <i class="fas fa-photo-video"></i> Browse Media
                                    </button>
                                </div>

                                @php
                                    $isEditorJs = false;
                                    $rawContent = old('content', $page->content);
                                    if ($rawContent) {
                                        $parsed = json_decode($rawContent, true);
                                        $isEditorJs = $parsed && isset($parsed['blocks']) && is_array($parsed['blocks']);
                                    }
                                @endphp

                                @if($isEditorJs)
                                    <div id="editorjs" class="mb-2"></div>
                                    <input type="hidden" name="content" id="content" value="{{ $rawContent }}">
                                    <small class="text-muted">Editor.js — click <strong>+</strong> to add blocks.</small>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Legacy Content</strong> — This page was created with the old HTML editor.
                                        Saving will convert it to the new Editor.js format.
                                        <button type="button" id="convertToEditor" class="btn btn-sm btn-warning ml-2">
                                            <i class="fas fa-exchange-alt"></i> Switch to Editor
                                        </button>
                                    </div>
                                    <textarea name="content" id="content" rows="15"
                                        class="form-control @error('content') is-invalid @enderror"
                                        placeholder="Enter HTML content...">{{ $rawContent }}</textarea>
                                    <div id="editorjs" class="d-none"></div>
                                    @error('content')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="meta_title">Meta Title (SEO)</label>
                                        <input type="text" name="meta_title" id="meta_title" class="form-control"
                                            value="{{ old('meta_title', $page->meta_title) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control"
                                            value="{{ old('sort_order', $page->sort_order) }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="draft" {{ old('status', $page->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ old('status', $page->status) == 'published' ? 'selected' : '' }}>Published</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description (SEO)</label>
                                <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Page
                            </button>
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('admin.media.browse-modal')
@endsection

@push('scripts')

{{-- Editor.js CDN scripts — must load before any tool/init code --}}
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.7"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.1"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@1.9.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/paragraph@2.11.3"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@2.6.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@1.6.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@1.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@2.9.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image@1.6.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@2.0.4"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/code@2.9.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.4"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/link@2.6.2"></script>

{{-- MediaTextTool class — always defined regardless of EditorJS state --}}
<script>
/**
 * Custom Media & Text block — like WordPress Media & Text
 */
class MediaTextTool {
  static get toolbox() {
    return {
      title: 'Media & Text',
      icon: '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="7" height="16" rx="1" fill="currentColor"/><rect x="10" y="1" width="7" height="5" rx="1" fill="currentColor"/><rect x="10" y="7" width="7" height="3" rx="1" fill="currentColor"/><rect x="10" y="11" width="7" height="6" rx="1" fill="currentColor"/></svg>'
    };
  }
  constructor({ data, api }) {
    this.api = api;
    this.data = {
      mediaUrl: data.mediaUrl || '',
      title: data.title || '',
      text: data.text || '',
      align: data.align || 'left'
    };
    this.wrapper = null;
    this.nodes = {};
  }
  render() {
    this.wrapper = document.createElement('div');
    this.wrapper.className = 'media-text-wrap' + (this.data.align === 'right' ? ' mt-reverse' : '');
    var mediaSide = document.createElement('div');
    mediaSide.className = 'mt-media';
    var mLabel = document.createElement('div');
    mLabel.className = 'mt-label';
    mLabel.textContent = 'Image URL';
    mediaSide.appendChild(mLabel);
    this.nodes.urlInput = document.createElement('input');
    this.nodes.urlInput.type = 'text';
    this.nodes.urlInput.className = 'mt-url-input';
    this.nodes.urlInput.placeholder = 'https://example.com/image.jpg';
    this.nodes.urlInput.value = this.data.mediaUrl;
    this.nodes.urlInput.addEventListener('input', this._updatePreview.bind(this));
    mediaSide.appendChild(this.nodes.urlInput);
    var browseBtn = document.createElement('button');
    browseBtn.type = 'button';
    browseBtn.className = 'mt-browse';
    browseBtn.textContent = '📁 Browse Media';
    var self = this;
    browseBtn.addEventListener('click', function() {
      window._mediaTextCallback = function(url) {
        self.nodes.urlInput.value = url;
        self._updatePreview();
      };
      $('#browseMediaModal').modal('show');
    });
    mediaSide.appendChild(browseBtn);
    this.nodes.preview = document.createElement('div');
    this.nodes.preview.className = 'mt-preview';
    mediaSide.appendChild(this.nodes.preview);
    var textSide = document.createElement('div');
    textSide.className = 'mt-text';
    var tLabel = document.createElement('div');
    tLabel.className = 'mt-label';
    tLabel.textContent = 'Title';
    textSide.appendChild(tLabel);
    this.nodes.titleInput = document.createElement('input');
    this.nodes.titleInput.type = 'text';
    this.nodes.titleInput.className = 'mt-title';
    this.nodes.titleInput.placeholder = 'Heading text...';
    this.nodes.titleInput.value = this.data.title;
    textSide.appendChild(this.nodes.titleInput);
    var cLabel = document.createElement('div');
    cLabel.className = 'mt-label';
    cLabel.textContent = 'Content';
    textSide.appendChild(cLabel);
    this.nodes.contentEditable = document.createElement('div');
    this.nodes.contentEditable.className = 'mt-content';
    this.nodes.contentEditable.contentEditable = true;
    this.nodes.contentEditable.innerHTML = this.data.text;
    this.nodes.contentEditable.setAttribute('data-placeholder', 'Write your content here...');
    textSide.appendChild(this.nodes.contentEditable);
    if (this.data.align === 'right') {
      this.wrapper.appendChild(textSide);
      this.wrapper.appendChild(mediaSide);
    } else {
      this.wrapper.appendChild(mediaSide);
      this.wrapper.appendChild(textSide);
    }
    this._updatePreview();
    return this.wrapper;
  }
  _updatePreview() {
    var url = this.nodes.urlInput.value;
    if (!url) { this.nodes.preview.innerHTML = '<span style="color:#999;font-size:13px;">🖼 Preview</span>'; return; }
    this.nodes.preview.innerHTML = '<img src="' + url.replace(/"/g,'&quot;') + '" alt="" onerror="this.parentElement.innerHTML=\'<span class=mt-error>Invalid image URL</span>\'">';
  }
  save() {
    return {
      mediaUrl: this.nodes.urlInput.value,
      title: this.nodes.titleInput.value,
      text: this.nodes.contentEditable.innerHTML,
      align: this.data.align
    };
  }
  renderSettings() {
    var wrap = document.createElement('div');
    wrap.style.cssText = 'padding:8px 12px;';
    var label = document.createElement('div');
    label.textContent = 'Media position:';
    label.style.cssText = 'font-size:11px;color:#888;margin-bottom:6px;font-weight:500;text-transform:uppercase;';
    var btnLeft = document.createElement('button');
    btnLeft.type = 'button';
    btnLeft.textContent = '← Left';
    btnLeft.style.cssText = 'padding:6px 14px;margin-right:6px;border:1px solid #d2d6de;border-radius:4px;cursor:pointer;font-size:13px;';
    var btnRight = document.createElement('button');
    btnRight.type = 'button';
    btnRight.textContent = 'Right →';
    btnRight.style.cssText = 'padding:6px 14px;border:1px solid #d2d6de;border-radius:4px;cursor:pointer;font-size:13px;';
    var self = this;
    function updateBtns() {
      btnLeft.style.background = self.data.align === 'left' ? '#3c8dbc' : '#fff';
      btnLeft.style.color = self.data.align === 'left' ? '#fff' : '#333';
      btnRight.style.background = self.data.align === 'right' ? '#3c8dbc' : '#fff';
      btnRight.style.color = self.data.align === 'right' ? '#fff' : '#333';
    }
    btnLeft.addEventListener('click', function() {
      self.data.align = 'left';
      self.wrapper.classList.remove('mt-reverse');
      updateBtns();
    });
    btnRight.addEventListener('click', function() {
      self.data.align = 'right';
      self.wrapper.classList.add('mt-reverse');
      updateBtns();
    });
    updateBtns();
    wrap.appendChild(label);
    wrap.appendChild(btnLeft);
    wrap.appendChild(btnRight);
    return wrap;
  }
  static get sanitize() {
    return { text: { br: true, b: true, i: true, u: true, a: { href: true, target: true }, ul: true, ol: true, li: true } };
  }
}
</script>

@if($isEditorJs)
<script>
$(function() {
    const rawContent = $('#content').val();
    let editorData = null;

    if (rawContent) {
        try {
            const parsed = JSON.parse(rawContent);
            if (parsed && parsed.blocks && Array.isArray(parsed.blocks)) {
                editorData = parsed;
            }
        } catch (e) {}
    }

    window.editor = new EditorJS({
        holder: 'editorjs',
        tools: {
            header: {
                class: Header,
                config: { levels: [2, 3, 4], defaultLevel: 2 }
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: '{{ route('admin.editor-images.upload') }}',
                        byUrl: '{{ route('admin.editor-images.fetch-url') }}',
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            },
            simpleImage: {
                class: SimpleImage,
                inlineToolbar: true,
                config: {
                    placeholder: 'Paste image URL...'
                }
            },
            mediaText: {
                class: MediaTextTool,
                inlineToolbar: true
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true
            },
            quote: {
                class: Quote,
                inlineToolbar: true
            },
            checklist: {
                class: Checklist,
                inlineToolbar: true
            },
            delimiter: Delimiter,
            warning: {
                class: Warning,
                inlineToolbar: true
            },
            table: {
                class: Table,
                inlineToolbar: true,
                config: {
                    rows: 2,
                    cols: 3
                }
            },
            code: {
                class: CodeTool,
                inlineToolbar: false
            },
            embed: {
                class: Embed,
                config: {
                    services: {
                        youtube: true,
                        vimeo: true,
                        twitter: true,
                        instagram: true,
                        facebook: true,
                        twitch: true,
                        giphy: true,
                        codepen: true,
                        soundcloud: true
                    },
                    inlineToolbar: true
                }
            },
            linkTool: {
                class: LinkTool,
                config: {
                    endpoint: '{{ route('admin.pages.link-preview') }}',
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            }
        },
        placeholder: 'Start writing your page content...',
        data: editorData || {
            blocks: [
                { type: 'paragraph', data: { text: 'Start writing...' } }
            ]
        }
    });

    // Callback for Browse Media modal — supports both Image Tool and Media & Text
    window.onBrowseMediaSelect = function(url) {
        if (window._mediaTextCallback) {
            window._mediaTextCallback(url);
            window._mediaTextCallback = null;
            return;
        }
        if (window.editor) {
            window.editor.blocks.insert('image', {
                file: { url: url },
                caption: '',
                stretched: false,
                withBorder: false,
                withBackground: false
            });
        }
    };

    $('#pageForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        window.editor.save().then((outputData) => {
            $('#content').val(JSON.stringify(outputData));
            this.submit();
        }).catch((error) => {
            console.error('Editor.js save failed:', error);
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Page');
            alert('Failed to save editor content. Please try again.');
        });
    });
});
</script>
@else
<script>
$(function() {
    // "Switch to Editor" button: hides textarea, shows Editor.js
    $('#convertToEditor').on('click', function() {
        if (!confirm('Convert to Editor.js? The current HTML will be loaded as editable blocks.')) return;

        const rawHtml = $('#content').val();

        // Hide textarea, show editorjs container
        $('#content').hide();
        $('#editorjs').removeClass('d-none');
        $(this).closest('.alert').hide();

        // Load CDN scripts dynamically
        const scripts = [
            'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.7',
            'https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.1',
            'https://cdn.jsdelivr.net/npm/@editorjs/list@1.9.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/paragraph@2.11.3',
            'https://cdn.jsdelivr.net/npm/@editorjs/quote@2.6.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/checklist@1.6.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.4.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/warning@1.4.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/simple-image@1.6.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/table@2.0.4',
            'https://cdn.jsdelivr.net/npm/@editorjs/code@2.9.0',
            'https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.4',
            'https://cdn.jsdelivr.net/npm/@editorjs/link@2.6.2'
        ];

        let loaded = 0;
        scripts.forEach(src => {
            const s = document.createElement('script');
            s.src = src;
            s.onload = function() {
                loaded++;
                if (loaded === scripts.length) {
                    initEditorJs(rawHtml);
                }
            };
            document.head.appendChild(s);
        });
    });

    function initEditorJs(rawHtml) {
        // Callback for Browse Media modal — supports both Image Tool and Media & Text
        window.onBrowseMediaSelect = function(url) {
            if (window._mediaTextCallback) {
                window._mediaTextCallback(url);
                window._mediaTextCallback = null;
                return;
            }
            if (window.editor) {
                window.editor.blocks.insert('image', {
                    file: { url: url },
                    caption: '',
                    stretched: false,
                    withBorder: false,
                    withBackground: false
                });
            }
        };

        window.editor = new EditorJS({
            holder: 'editorjs',
            tools: {
                header: {
                    class: Header,
                    config: { levels: [2, 3, 4], defaultLevel: 2 }
                },
                list: {
                    class: List,
                    inlineToolbar: true
                },
                mediaText: {
                    class: MediaTextTool,
                    inlineToolbar: true
                },
                simpleImage: {
                    class: SimpleImage,
                    inlineToolbar: true,
                    config: {
                        placeholder: 'Paste image URL...'
                    }
                },
                paragraph: {
                    class: Paragraph,
                    inlineToolbar: true
                },
                quote: {
                    class: Quote,
                    inlineToolbar: true
                },
                checklist: {
                    class: Checklist,
                    inlineToolbar: true
                },
                delimiter: Delimiter,
                warning: {
                    class: Warning,
                    inlineToolbar: true
                },
                table: {
                    class: Table,
                    inlineToolbar: true,
                    config: {
                        rows: 2,
                        cols: 3
                    }
                },
                code: {
                    class: CodeTool,
                    inlineToolbar: false
                },
                embed: {
                    class: Embed,
                    config: {
                        services: {
                            youtube: true,
                            vimeo: true,
                            twitter: true,
                            instagram: true,
                            facebook: true,
                            twitch: true,
                            giphy: true,
                            codepen: true,
                            soundcloud: true
                        },
                        inlineToolbar: true
                    }
                },
                linkTool: {
                    class: LinkTool,
                    config: {
                        endpoint: '{{ route('admin.pages.link-preview') }}',
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                }
            },
            placeholder: 'Start writing your page content...',
            data: {
                blocks: [
                    {
                        type: 'warning',
                        data: {
                            title: 'Legacy HTML Converted',
                            message: 'Your existing HTML has been loaded as editable blocks. When you save, this page will use the new Editor.js format.'
                        }
                    },
                    { type: 'paragraph', data: { text: rawHtml } }
                ]
            }
        });

        // Update form submit handler
        $('#pageForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#submitBtn');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            window.editor.save().then((outputData) => {
                $('#content').val(JSON.stringify(outputData));
                this.submit();
            }).catch((error) => {
                console.error('Editor.js save failed:', error);
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Page');
                alert('Failed to save editor content. Please try again.');
            });
        });
    }
});
</script>
@endif
@endpush

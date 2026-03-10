import './bootstrap';
import Alpine from 'alpinejs';

import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Link,
    BlockQuote,
    List,
    Table,
    TableToolbar,
    Undo,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    ImageUpload
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

window.Alpine = Alpine;
Alpine.start();

class BlogUploadAdapter {
    constructor(loader, options) {
        this.loader = loader;
        this.options = options || {};
        this.xhr = null;
    }

    upload() {
        return this.loader.file.then(file => {
            return new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            });
        });
    }

    abort() {
        if (this.xhr) {
            this.xhr.abort();
        }
    }

    _initRequest() {
        this.xhr = new XMLHttpRequest();
        this.xhr.open('POST', this.options.uploadUrl, true);
        this.xhr.responseType = 'json';

        if (this.options.csrfToken) {
            this.xhr.setRequestHeader('X-CSRF-TOKEN', this.options.csrfToken);
        }

        this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }

    _initListeners(resolve, reject, file) {
        const xhr = this.xhr;
        const genericErrorText = `Não foi possível enviar o arquivo: ${file.name}.`;

        xhr.addEventListener('error', () => reject(genericErrorText));
        xhr.addEventListener('abort', () => reject('Upload abortado.'));
        xhr.addEventListener('load', () => {
            const response = xhr.response;

            if (!response || xhr.status >= 400 || !response.url) {
                return reject(response?.message || genericErrorText);
            }

            resolve({
                default: response.url
            });
        });

        if (xhr.upload) {
            xhr.upload.addEventListener('progress', evt => {
                if (evt.lengthComputable) {
                    this.loader.uploadTotal = evt.total;
                    this.loader.uploaded = evt.loaded;
                }
            });
        }
    }

    _sendRequest(file) {
        const data = new FormData();

        data.append('upload', file);

        if (this.options.draftToken) {
            data.append('draft_token', this.options.draftToken);
        }

        if (this.options.postId) {
            data.append('post_id', this.options.postId);
        }

        this.xhr.send(data);
    }
}

function BlogUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = loader => {
        return new BlogUploadAdapter(loader, window.blogEditorUploadConfig || {});
    };
}

window.BlogCkeditor = {
    ClassicEditor,
    config: {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Link,
            BlockQuote,
            List,
            Table,
            TableToolbar,
            Undo,
            Image,
            ImageToolbar,
            ImageCaption,
            ImageStyle,
            ImageResize,
            ImageUpload
        ],
        toolbar: [
            'undo', 'redo',
            '|',
            'heading',
            '|',
            'bold', 'italic', 'underline', 'strikethrough',
            '|',
            'link', 'blockQuote',
            '|',
            'bulletedList', 'numberedList',
            '|',
            'insertTable',
            'uploadImage'
        ],
        image: {
            toolbar: [
                'imageTextAlternative',
                '|',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side'
            ]
        },
        extraPlugins: [BlogUploadAdapterPlugin],
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        }
    }
};